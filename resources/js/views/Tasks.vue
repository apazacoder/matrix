<template>
  <div id="main">
    <div class="title-section"></div>
    <v-card>
      <v-data-table
        class="elevation-1 centered-th centered-td three-actions tasks-table"
        loading-text="Cargando... Espere por favor"
        dense
        :headers="headers"
        :items="tasks"
        :loading="isProcessing"
        :footer-props="tableFooterProps"
        :options="tableOptions"
        :search="search"
      >
        <template v-slot:item.user="{item}">
          <span v-if="item.user.first_name" v-text="item.user.first_name"></span>
          <span v-if="item.user.second_name" v-text="item.user.second_name"></span>
          <span v-if="item.user.first_surname" v-text="item.user.first_surname"></span>
          <span v-if="item.user.second_surname" v-text="item.user.second_surname"></span>
        </template>
        <template v-slot:item.percentage="{item}">
          <v-progress-linear
            v-model="item.percentage"
            color="#4DB6AC"
            :value="Math.ceil(item.percentage)"
            height="25"
          >
            <strong>{{ Math.ceil(item.percentage) }}%</strong>
          </v-progress-linear>

        </template>

        <template v-slot:top>
          <v-toolbar flat color="white" class="task-toolbar">
            <v-toolbar-title v-text="plural"></v-toolbar-title>
            <v-divider
              class="mx-4"
              inset
              vertical
            ></v-divider>
            <v-spacer></v-spacer>
            <v-text-field
              v-model="search"
              append-icon="mdi-magnify"
              label="Buscar"
              single-line
              hide-details
            ></v-text-field>
            <v-spacer></v-spacer>
            <v-dialog v-model="dialog" content-class="v-dialog-large v-dialog-dense">
              <template v-slot:activator="{ on }">
                <v-btn v-if="$can('create', 'tasks')"
                  class="btn-green" v-on="on" @click="showForAction('create')">
                  <v-icon left>mdi-plus</v-icon>
                  <span>Crear <span v-text="singular"></span></span>
                </v-btn>
              </template>
              <v-card>
                <v-card-title>
                  <span class="headline">{{ formTitle }}</span>
                </v-card-title>
                <v-card-text>
                  <v-container class="without-padding dense">
                    <v-row>
                      <v-col cols="12" sm="12" md="12">
                        <v-text-field v-model="editedItem.issue"
                                      dense @keyup.enter="save"
                                      :disabled="editionIsDisabled || directorLock"
                                      label="Tarea"></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="12" md="12">
                        <v-textarea v-model="editedItem.description"
                                    rows="3"

                                    :disabled="editionIsDisabled || directorLock"
                                    label="Descripción"></v-textarea>
                      </v-col>
                    </v-row>
                    <div class="milestones-table-wrapper">
                      <table class="milestones-table">
                        <thead>
                        <tr>
                          <th colspan="4">
                            <v-btn @click="addMilestone" class="btn-green" color="primary"
                                   :disabled="directorLock || editionIsDisabled"
                            >
                              <v-icon>mdi-plus</v-icon>
                              Agregar hito
                            </v-btn>
                          </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(milestone, mindex) in editedItem.milestones">
                          <td v-text="mindex+1"></td>
                          <td>
                            <v-textarea v-model="milestone.description"
                                        rows="2"
                                        dense
                                        @keyup="restrictDates('ta')"
                                        :disabled="editionIsDisabled || directorLock"
                                        label="Descripción"></v-textarea>
                          </td>
                          <td>
                            <v-menu
                              v-model="milestone.milestone_date"
                              :close-on-content-click="false"
                              :nudge-right="40"
                              transition="scale-transition"
                              offset-y
                              min-width="auto"
                              dense
                            >
                              <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                  v-model="milestone.scheduled_at"
                                  label="Fecha límite"
                                  prepend-icon="mdi-calendar"
                                  readonly
                                  v-bind="attrs"
                                  v-on="on"
                                  dense
                                  :disabled="editionIsDisabled || directorLock"
                                ></v-text-field>
                              </template>
                              <v-date-picker
                                v-model="milestone.scheduled_at"
                                :min="milestone.min_date"
                                @input="milestone.milestone_date = false"
                                @change="restrictDates('dp')"
                                dense
                              ></v-date-picker>
                            </v-menu>
                          </td>
                          <td>
                            <v-select
                              :disabled="currentAction=='create' || editionIsDisabled || (milestone.status == 'completado' && directorLock)"
                              v-model="milestone.status"
                              :items="['completado', 'no completado']"
                              label="Estado"
                            ></v-select>
                          </td>
                          <td>
                            <v-btn @click="removeMilestone(mindex)" class="btn-red" color="error" tabindex="-1"
                                   :disabled="directorLock || editionIsDisabled"
                            >
                              <v-icon>mdi-trash-can</v-icon>
                            </v-btn>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                  </v-container>
                  <div class="progress-wrapper">
                    <v-progress-linear
                      :active="isProcessing"
                      :indeterminate="true"
                      :rounded="false"
                      color="teal darken-2"
                    ></v-progress-linear>
                  </div>
                </v-card-text>
                <v-container v-if="errors.length > 0" class="without-vertical-padding">
                  <v-alert
                    text
                    prominent
                    type="error"
                    icon="mdi-alert-circle"
                  >
                    <div v-for="error in errors" v-text="error"></div>
                  </v-alert>
                </v-container>
                <v-container>
                  <v-alert v-if="currentAction == 'create' || currentAction == 'update'"
                           text
                           outlined
                           type="warning"
                           dense
                  >Por favor verifique antes de guardar, una vez guardado <strong>no podrá modificar la tarea ni los
                    hitos</strong>, solo podrá marcar los hitos como completados
                  </v-alert>
                </v-container>
                <v-card-actions>
                  <v-alert v-if="currentAction == 'delete'"
                           icon="mdi-alert"
                           dense text type="warning">
                    ¿Confirma que quiere borrar?
                  </v-alert>
                  <v-spacer></v-spacer>
                  <v-btn :color="currentAction == 'delete' ? 'red lighten-1' : 'teal darken-1'"
                         text
                         @click="save"
                         :disabled="isProcessing"
                         v-if="currentAction != 'read'">
                    <v-icon left v-if="currentAction != 'delete'">mdi-content-save</v-icon>
                    <v-icon left v-if="currentAction == 'delete'">mdi-trash-can</v-icon>
                    <span v-text="currentAction == 'delete' ? 'Borrar': 'Guardar'"></span>
                  </v-btn>
                  <v-btn color="blue darken-1" text @click="close"
                         :disabled="isProcessing">
                    <v-icon left>mdi-close</v-icon>
                    <span v-text="currentAction == 'read' ? 'Cerrar': 'Cancelar'"></span>
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
            <v-dialog v-model="chartDialog" content-class="v-dialog-dense v-dialog-extra-large graph-dialog">
              <v-card>
                <v-card-title>
                  <span id="currentTask"></span>
                </v-card-title>
                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-col>
                        <v-simple-table dense>
                          <template v-slot:default>
                            <thead>
                            <tr>
                              <th colspan="3" style="text-align:center;">HITOS</th>
                            </tr>
                            <tr>
                              <th class="text-left">
                                Nombre
                              </th>
                              <th class="text-left">
                                Fecha límite
                              </th>
                              <th class="text-left">
                                Estado
                              </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr
                              v-for="(milestone, mi) in milestones"
                              :key="milestone+''+mi"
                            >
                              <td>{{ milestone.description }}</td>
                              <td>{{ milestone.scheduled_at }}</td>
                              <td :class="milestone.curStatus">
                                <span v-if="milestone.curStatus =='completed-on-time'">CUMPLIÓ</span>
                                <span v-if="milestone.curStatus =='completed-late'">CON RETRASO</span>
                                <span v-if="milestone.curStatus =='to-be-started'">NO SE EMPEZÓ</span>
                                <span v-if="milestone.curStatus =='not-completed-late'">NO CUMPLIÓ</span>
                              </td>
                            </tr>
                            </tbody>
                          </template>
                        </v-simple-table>

                      </v-col>
                      <v-col>
                        <canvas id="taskChart" width="400" height="400"></canvas>
                        <div class="bg-descriptions">
                          <span class="bg-completed-on-time bg-meaning"></span> CUMPLIÓ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          <span class="bg-completed-late bg-meaning"></span> CON RETRASO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>
                          <span class="bg-to-be-started bg-meaning"></span> NO SE EMPEZÓ&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          <span class="bg-not-completed-late bg-meaning"></span> NO CUMPLIÓ
                        </div>
                      </v-col>
                    </v-row>
                    <v-icon  class="left-nav" @click="prevChart()" >mdi-chevron-left</v-icon>
                    <v-icon class="right-nav" @click="nextChart()" >mdi-chevron-right</v-icon>
                  </v-container>
                </v-card-text>
                <v-card-actions>
                  <v-btn color="blue darken-1" text @click="closeChart" class="btn-close-chart"
                         :disabled="isProcessing">
                    <v-icon left>mdi-close</v-icon>
                    <span v-text="'Cerrar'"></span>
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </v-toolbar>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-icon
            :color="readColor"
            v-if="$can('read', 'tasks')"
            class="mr-2"
            :disabled="isProcessing"
            @click="showForAction('read', item)">
            mdi-eye
          </v-icon>
          <v-icon
            :color="updateColor"
            v-if="$can('update', 'tasks')"
            class="mr-2"
            :disabled="isProcessing"
            @click="showForAction('update', item)">
            mdi-pencil
          </v-icon>
          <v-icon
            :color="deleteColor"
            v-if="$can('delete', 'tasks')"
            :disabled="isProcessing"
            @click="showForAction('delete', item)">
            mdi-delete
          </v-icon>
          <v-icon
            color="indigo lighten-3"
            v-if="$can('graph', 'tasks')"
            :disabled="isProcessing"
            @click="showChart(item)">
            mdi-chart-donut
          </v-icon>
        </template>
        <template v-slot:no-data>
          No existen registros
        </template>
      </v-data-table>
    </v-card>
  </div>
</template>

<script>
import {dataTableOptions as dtOptions} from './../vuetify-custom';

dtOptions.tableOptions.itemsPerPage = 14;

require('./../axioswrapper');

import moment from 'moment';

const Chart = require('chart.js');

export default {
  data: () => ({
    plural: 'Tareas',
    singular: 'tarea',

    dialog: false,
    chartDialog: false,
    headers: [
      {text: 'Tarea', value: 'issue'},
      {text: 'Descripción', value: 'description'},
      {text: 'Responsable', value: 'user'},
      {text: 'Porcentaje completado', value: 'percentage'},
      {text: 'Acciones', value: 'actions', sortable: false},
    ],
    tasks: [],
    editedIndex: -1, // -1: para crear usuario, cualquier otro para editar usuario
    editedItem: {
      issue: '',
      description: '',
      milestones: []
    },
    defaultItem: {
      issue: '',
      description: '',
      milestones: []
    },
    errors: [],
    currentAction: '',

    isDirector: false,
    isGuest: false,
    isProcessing: true,

    // relations:
    units: [],
    accounts: [],
    classifiers: [],

    // table options
    tableFooterProps: dtOptions.tableFooterProps,
    tableOptions: dtOptions.tableOptions,

    createColor: dtOptions.createColor,
    readColor: dtOptions.readColor,
    updateColor: dtOptions.updateColor,
    deleteColor: dtOptions.deleteColor,
    // search
    search: '',

    // milestonnes for the graph
    milestones: null,
  }),
  computed: {
    formTitle() {
      switch (this.currentAction) {
        case 'create':
          return 'Crear ' + this.singular;
          break;
        case 'read':
          return 'Ver ' + this.singular;
          break;
        case 'update':
          return 'Editar ' + this.singular;
          break;
        case 'delete':
          return 'Borrar ' + this.singular;
          break;
      }
    },
    editionIsDisabled() {
      // deshabilitamos edición si está cargando, viendo o borrando
      return this.isProcessing || this.currentAction == 'read' || this.currentAction == 'delete';
    },
    directorLock() {
      return this.currentAction == 'update' && this.isDirector;
    }
  },
  watch: {

  },
  created() {
    this.initialize();
  },
  mounted(){
    console.log("here xxx");
  },
  methods: {
    initialize() {
      this.getArticles();
    },
    getChartIndex(){
      console.log(JSON.stringify(this.editedItem));
      for(let i = 0; i<= this.tasks.length; i++){
        if (this.tasks[i].id == this.editedItem.id){
          return i; // index
        }
      }
      return -1; // not found
    },
    prevChart(){
      let curChartIndex = this.getChartIndex();
      if ( curChartIndex == 0  ){
         // do nothing
        showInfoMessage("Ha llegado al inicio de las tareas");
      }else{
        this.showChart(this.tasks[curChartIndex -1 ]);
      }
    },
    nextChart(){
      let curChartIndex = this.getChartIndex();
      if ( curChartIndex + 1 == this.tasks.length ){
        // do nothing
        showInfoMessage("Ha llegado al final de las tareas");
      }else{
        // switch and show
        this.showChart(this.tasks[curChartIndex+1]);
      }
    },
    showChart(item = null) {
      this.editedItem = Object.assign({}, item);

      this.chartDialog = true;
      console.log("showing chart dialog");
      setTimeout(() => {


        document.getElementById("currentTask").innerText = item.issue;
        console.log("tried to show chart");
        console.log(JSON.stringify(item));

        this.milestones = item.milestones;

        let completedOnTime = [];
        let completedLate = [];
        let toBeStarted = [];
        let notCompletedLate = [];

        for (let i = 0; i < this.milestones.length; i++) {
          let ms = this.milestones[i];
          if (ms.status == "completado" && ms.completed_at ? moment(ms.completed_at, 'YYYY-MM-DD HH:mm:ss', true).isSameOrBefore(moment(ms.scheduled_at, 'YYYY-MM-DD', true)
          ) : false) {
            completedOnTime.push(ms);
            this.milestones[i]["curStatus"] = "completed-on-time";
          } else if (ms.status == "completado" && ms.completed_at ? moment(ms.completed_at, 'YYYY-MM-DD HH:mm:ss', true).isAfter(moment(ms.scheduled_at, 'YYYY-MM-DD', true)
          ) : false) {
            completedLate.push(ms);
            this.milestones[i]["curStatus"] = "completed-late";
          } else if (ms.status == "no completado" && moment().isAfter(moment(ms.scheduled_at, 'YYYY-MM-DD', true)
          )) {
            notCompletedLate.push(ms);
            this.milestones[i]["curStatus"] = "not-completed-late";
          } else if (ms.status == "no completado" && moment().isSameOrBefore(moment(ms.scheduled_at, 'YYYY-MM-DD', true))) {
            toBeStarted.push(ms);
            this.milestones[i]["curStatus"] = "to-be-started";
          }
        }


        // let completedOnTime = milestones.filter(
        //   ms => ); // completed and completed_at prev or equal to scheduled_at
        // let completedLate = milestones.filter(
        //   ms => ms.status == "completado" && ms.completed_at ? moment(ms.completed_at, 'YYYY-MM-DD HH:mm:ss', true).isAfter(moment(ms.scheduled_at, 'YYYY-MM-DD', true)
        //   ) : false); // completed and completed_at greater than scheduled_at
        // let toBeStarted = milestones.filter(
        //   ms => ms.status == "no completado" && ms.completed_at === null
        // ); // no completed and completed_at equals null
        // let notCompletedLate = milestones.filter(
        //   ms => ms.status == "no completado" && ms.completed_at ? moment(ms.completed_at, 'YYYY-MM-DD HH:mm:ss', true).isAfter(moment(ms.scheduled_at, 'YYYY-MM-DD', true)
        //   ) : false); // no completed and completed_at greater than scheduled_at

        console.log(JSON.stringify(completedOnTime));
        console.log(JSON.stringify(completedLate));
        console.log(JSON.stringify(toBeStarted));
        console.log(JSON.stringify(notCompletedLate));

        console.log('---');

        console.log(completedOnTime.length);
        console.log(completedLate.length);
        console.log(toBeStarted.length);
        console.log(notCompletedLate.length);

        let completedOnTimeLength = completedOnTime.length;
        let completedLateLength = completedLate.length;
        let toBeStartedLength = toBeStarted.length;
        let notCompletedLateLength = notCompletedLate.length;

        let allLength = completedOnTimeLength + completedLateLength + toBeStartedLength + notCompletedLateLength;
        console.log("alllength");
        console.log(allLength);

        let datas = [];
        let backgrounds = [];
        let allLabels = [];

        for (let a = 1; a <= completedOnTimeLength; a++) { // completado
          //datas.push(completedOnTimeLength / allLength);
          datas.push("1");
          console.log("1", completedOnTimeLength / allLength)
          backgrounds.push('#2E7D32');
          allLabels.push(completedOnTime[a - 1].description);
        }
        for (let b = 1; b <= completedLateLength; b++) {
          //datas.push(completedLateLength / allLength);
          datas.push("1");
          console.log("1", completedOnTimeLength / allLength)
          backgrounds.push('#FF7043');
          allLabels.push(completedLate[b - 1].description);
        }
        for (let c = 1; c <= toBeStartedLength; c++) {
          //datas.push(toBeStartedLength / allLength);
          datas.push("1");
          console.log("1", completedOnTimeLength / allLength)
          backgrounds.push('#CFD8DC');
          allLabels.push(toBeStarted[c - 1].description);
        }
        for (let d = 1; d <= notCompletedLateLength; d++) {
          //datas.push(notCompletedLateLength / allLength);
          datas.push("1");
          console.log("1", completedOnTimeLength / allLength)
          backgrounds.push('#B71C1C');
          allLabels.push(notCompletedLate[d - 1].description);
        }
        console.log("datas", datas);
        console.log("backgrounds", backgrounds);

        window.centerText = ((completedOnTimeLength + completedLateLength) * 100 / allLength).toFixed(1) + "%"
        Chart.pluginService.register({
          beforeDraw: function (chart) {
            var width = chart.chart.width,
              height = chart.chart.height,
              ctx = chart.chart.ctx;

            ctx.restore();
            var fontSize = (height / 114).toFixed(2);
            ctx.font = fontSize + "em sans-serif";
            ctx.textBaseline = "middle";

            var text = window.centerText,
              textX = Math.round((width - ctx.measureText(text).width) / 2),
              textY = height / 2;

            ctx.fillText(text, textX, textY);
            ctx.save();
          }
        });

        // generate graph data
        let ctx = document.getElementById("taskChart");
        let data = {
          datasets: [{
            data: datas,
            backgroundColor: backgrounds
            // '#2E7D32', // 'Completado a tiempo',
            // '#FF7043', // 'Completado a destiempo',
            // '#CFD8DC', // 'Por iniciar',
            // '#B71C1C', // 'No completado a destiempo',
            // ],
          }],

          // These labels appear in the legend and in the tooltips when hovering different arcs
          labels: [], // allLabels

          //   'Completado a tiempo',
          //   'Completado a destiempo',
          //   'Por iniciar',
          //   'No completado a destiempo',
          // ]
        };

        let options = {
          options: {
            responsive: true,
            legend: {
              display: false
            },
            plugins: {
              datalabels: false, // Removing this line shows the datalabels again
            }
          }
        };

        var myDoughnutChart = new Chart(ctx, {
          type: 'doughnut',
          data: data,
          options: options
        });
      }, 1000);


    },

    restrictDates(origin = null){
      console.log("called from: ", origin);
      console.log("function called!");

      let prevMin = moment().format("YYYY-MM-DD");
      console.log("me", this.editedItem.milestones.length);
      for (let i = 0; i < this.editedItem.milestones.length; i++){
        if ( this.editedItem.milestones[i].min_date == undefined ){
          // set current date
          this.editedItem.milestones[i].min_date = prevMin;
        }
        if ( this.editedItem.milestones[i].scheduled_at ){
          prevMin = this.editedItem.milestones[i].scheduled_at;
        }else if (this.editedItem.milestones[i].min_date){
          prevMin = this.editedItem.milestones[i].min_date;
        }
      }
    },
    closeChart() {
      this.chartDialog = false;
    },
    calculatePercentages(){
      for(let i = 0; i < this.tasks.length; i++){
        console.log(this.tasks[i].description);
        let completedOnTime = [];
        let completedLate = [];
        let toBeStarted = [];
        let notCompletedLate = [];
        for(let j = 0; j < this.tasks[i].milestones.length; j++){

          let ms = this.tasks[i].milestones[j];
          console.log("ms", JSON.stringify(ms));
          console.log("ms.status", ms.status);
          if (ms.status == "completado" && ms.completed_at ? moment(ms.completed_at, 'YYYY-MM-DD HH:mm:ss', true).isSameOrBefore(moment(ms.scheduled_at, 'YYYY-MM-DD', true)
          ) : false) {
            completedOnTime.push(ms);
          } else if (ms.status == "completado" && ms.completed_at ? moment(ms.completed_at, 'YYYY-MM-DD HH:mm:ss', true).isAfter(moment(ms.scheduled_at, 'YYYY-MM-DD', true)
          ) : false) {
            completedLate.push(ms);
          } else if (ms.status == "no completado" && moment().isAfter(moment(ms.scheduled_at, 'YYYY-MM-DD', true)
          )) {
            notCompletedLate.push(ms);
          } else if (ms.status == "no completado" && moment().isSameOrBefore(moment(ms.scheduled_at, 'YYYY-MM-DD', true))) {
            toBeStarted.push(ms);
          }
        }

        let completedOnTimeLength = completedOnTime.length;
        let completedLateLength = completedLate.length;
        let toBeStartedLength = toBeStarted.length;
        let notCompletedLateLength = notCompletedLate.length;

        let allLength = completedOnTimeLength + completedLateLength + toBeStartedLength + notCompletedLateLength;

        let percentage = ((completedOnTimeLength + completedLateLength) * 100 / allLength).toFixed(1)

        console.log("percentage", percentage);

        this.tasks[i].percentage = percentage;
        // end of calcultation
      }
    },
    // index
    getArticles(callback = null) {
      this.isProcessing = true;
      axiosGetApi('/tasks', res => {
        this.tasks = res.data.tasks;

        this.calculatePercentages();

        this.isDirector = res.data.is_director;
        this.isGuest = res.data.is_guest;
        console.log("this.isGuest", this.isGuest);
        // clear the timeout
        if(this.isGuest){
          console.log("is guest");
          $("#panel-wrapper").addClass("sidenav-collapsed");
          window.handleResize =  function(){
            console.log("empty handle");
          }
          clearTimeout(window.doit)
          window.setResizeHandler =  function(){
            console.log("empty setresize");
          }
        }

        // added date parameter to milestones for the picker
        for (let i in this.tasks.milestones) {
          this.tasks.milestones[i].min_date = false;
        }
        this.units = res.data.units;
        this.accounts = res.data.accounts;
        this.classifiers = res.data.classifiers;
        this.tasks = res.data.tasks;
        if (callback) {
          callback();
        }
        this.isProcessing = false;
      });
    },

    // update
    updateArticle() {
      this.isProcessing = true;
      let editedIndex = this.editedIndex;
      axiosPutApi(`/tasks/${this.editedItem.id}`, this.editedItem, res => {
        if (res.data.hasOwnProperty("errors") && res.data.errors.length > 0) {
          this.errors = res.data.errors;
          this.isProcessing = false;
        } else {
          this.getArticles(
            () => {
              setTimeout(() => {
                showSuccessMessage(res.data.okMessage);
                this.animateRow(editedIndex);
              }, 200);
              this.close();
            }
          );
        }
      });
    },

    animateRow(editedIndex) {
      let rowIndex = editedIndex % $("tbody tr").length + 1;
      $("tbody tr:nth-child(" + rowIndex + ")").addClass('updated-row');
    },

    storeArticle(allOk = null) {
      this.isProcessing = true;

      axiosPostApi('/tasks', this.editedItem, res => {
        if (res.data.hasOwnProperty("errors") && res.data.errors.length > 0) {
          this.errors = res.data.errors;
          this.isProcessing = false;
        } else {
          this.getArticles(
            () => {
              setTimeout(() => {
                showSuccessMessage(res.data.okMessage);
              }, 200);
              this.close();
            }
          )
        }
      });
    },

    destroyArticle() {
      this.isProcessing = true;

      axiosDeleteApi(`/tasks/${this.editedItem.id}`, {}, res => {
        if (res.data.hasOwnProperty("errors") && res.data.errors.length > 0) {
          this.errors = res.data.errors;
          this.isProcessing = false;
        } else {
          this.getArticles(
            () => {
              setTimeout(() => {
                showSuccessMessage(res.data.okMessage);
              }, 200);
              this.close();
            }
          )
        }
      });
    },
    restrictMilestones() {
      let current_datetime = new Date()
      let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate()
      console.log(formatted_date)

      for (let i = 0; this.editedItem.milestones.length; i++) {
        this.editedItem.milestones[0].min_date = formatted_date;
      }
    },
    showForAction(action, item = null) {
      this.currentAction = action;
      if (!item) {
        item = this.defaultItem;
        item.milestones = [];

      }
      this.editedIndex = this.tasks.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true

      // this.restrictMilestones();
      console.log("after restrict");
    },
    close() {
      this.errors = [];
      this.dialog = false

      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedItem.milestones = [];
        this.editedIndex = -1;
      })
    },

    save() {
      switch (this.currentAction) {
        case 'create':
          this.storeArticle();
          break;
        case 'read':
          this.close();
          break;
        case 'update':
          this.updateArticle();
          break;
        case 'delete':
          this.destroyArticle();
          break;
      }
    },

    accountFilter(item, queryText, itemText) {
      const textOne = item.name.toLowerCase() + item.account.toLowerCase();
      const searchText = queryText.toLowerCase()

      return textOne.indexOf(searchText) > -1
    },
    classifierFilter(item, queryText, itemText) {
      const textOne = item.name.toLowerCase() + item.account.toLowerCase();
      const searchText = queryText.toLowerCase()

      return textOne.indexOf(searchText) > -1
    },
    groupFilter(item, queryText, itemText) {
      const textOne = item.code.toLowerCase() + item.name.toLowerCase();
      const searchText = queryText.toLowerCase()

      return textOne.indexOf(searchText) > -1
    },

    addMilestone() {
      this.editedItem.milestones.push({
        description: "",
        scheduled_at: "",
        status: "no completado",
        completed_at: "",
      });
      this.restrictDates('mi');
    },
    removeMilestone(index) {
      this.editedItem.milestones.splice(index, 1);
    },
  },
}
</script>

<style scoped>
.completed-on-time {
  color: #2E7D32;
}

.completed-late {
  color: #FF7043;
}

.to-be-started {
  color: #546E7A;
}

.not-completed-late {
  color: #B71C1C;
}

.bg-meaning{
  display:inline-block;
  width: 33px;
  height: 11px;
}
.bg-completed-on-time {
  background-color: #2E7D32;
}

.bg-completed-late {
  background-color: #FF7043;
}

.bg-to-be-started {
  background-color: #546E7A;
}

.bg-not-completed-late {
  background-color: #B71C1C;
}
.bg-descriptions{
  text-align: center;
}

.left-nav, .right-nav{
  position: absolute;
  left: .7rem;
  top: 45%;
  font-size: 3rem;
  padding: .3rem;
}

.right-nav{
  right: .7rem;
  left:auto;
}
.btn-close-chart{
  margin: 0 auto;
}



</style>
