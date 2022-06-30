<template>
  <div id="main">
    <div class="title-section">
      <h1> Inicio </h1>
    </div>
    <v-card
      class="mx-auto"
    >
      <v-card-text>
        Bienvenido <span v-text="roles"></span>
        <v-row dense class="home-actions" style="display:none">
          <v-col cols="12" xs="12" sm="6" lg="3" v-for="(item, index) in graphsData" :key="'gd'+index">
            <v-card @click="navigateTo(item.route)" dark :class="'bg-'+(index+1)">
              <v-row class="pa-3">
                <v-col cols="6" class="data-row">
                  <span class="movs-qty" v-text="item.movsQty"></span>
                  <br>
                  <span class="card-name" v-text="item.cardName"></span>
                </v-col>
                <v-col cols="6">
                  <canvas :id="'chart-'+(index+1)"></canvas>
                </v-col>
              </v-row>
              <hr>
              <v-card-actions>
                <v-icon>mdi-av-timer</v-icon>&nbsp;
                actualizado el
                &nbsp;<span class="movs-date" v-text="item.movsDate"></span>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
    <v-container>

    </v-container>
  </div>
</template>

<script>
const Chart = require('chart.js');
export default {
  name: "Home",
  data() {
    return {
      roles: '',
      labels: ["", "", "", "", ""],
      points: [10, 20, 10, 15, 2],
      startAt: '',

      graphsData: [
        {
          // extras
          route: 'entry',
          movsQty: '3355', // 3555
          cardName: 'en proceso de ingreso', // ingresos
          movsDate: '2021-06-15 09:47:23', // 2021-06-15 09:47:23

          // chart
          labels: [],
          freqs: [],
          startAt: ''
        },
        {
          // extras
          route: 'entry',
          movsQty: '3355', // 3555
          cardName: 'ingresos', // ingresos
          movsDate: '2021-06-15 09:47:23', // 2021-06-15 09:47:23

          // chart
          labels: [],
          freqs: [],
          startAt: ''
        },
        {
          // extras
          route: 'entry',
          movsQty: '3355', // 3555
          cardName: 'asignaciones', // ingresos
          movsDate: '2021-06-15 09:47:23', // 2021-06-15 09:47:23

          // chart
          labels: [],
          freqs: [],
          startAt: ''
        },
        {
          // extras
          route: 'entry',
          movsQty: '3355', // 3555
          cardName: 'movimientos', // ingresos
          movsDate: '2021-06-15 09:47:23', // 2021-06-15 09:47:23

          // chart
          labels: [],
          freqs: [],
          startAt: ''
        },
        {
          // extras
          route: 'entry',
          movsQty: '3355', // 3555
          cardName: 'bajas', // ingresos
          movsDate: '2021-06-15 09:47:23', // 2021-06-15 09:47:23

          // chart
          labels: [],
          freqs: [],
          startAt: ''
        },
        {
          // extras
          route: 'entry',
          movsQty: '3355', // 3555
          cardName: 'devoluciones', // ingresos
          movsDate: '2021-06-15 09:47:23', // 2021-06-15 09:47:23

          // chart
          labels: [],
          freqs: [],
          startAt: ''
        },
        {
          // extras
          route: 'entry',
          movsQty: '3355', // 3555
          cardName: 'depreciaciones', // ingresos
          movsDate: '2021-06-15 09:47:23', // 2021-06-15 09:47:23

          // chart
          labels: [],
          freqs: [],
          startAt: ''
        },
        {
          // extras
          route: 'entry',
          movsQty: '3355', // 3555
          cardName: 'revalúos', // ingresos
          movsDate: '2021-06-15 09:47:23', // 2021-06-15 09:47:23

          // chart
          labels: [],
          freqs: [],
          startAt: ''
        }

      ]
    }
  },
  methods: {
    genGraphs() {


      for (let gi = 0; gi < this.graphsData.length; gi++) {
        new Chart(document.getElementById("chart-" + (gi + 1)), {
          type: 'bar',
          data: {
            graphData: [
              {
                labels: '',
              }
            ],
            labels: this.labels,
            datasets: [{
              barThickness: 18,
              data: this.points,
              hoverBackgroundColor: [
                'rgba(255, 255, 255, 1)',
                'rgba(255, 255, 255, 1)',
                'rgba(255, 255, 255, 1)',
                'rgba(255, 255, 255, 1)',
                'rgba(255, 255, 255, 1)',
              ],
              backgroundColor: [
                'rgba(255, 255, 255, 0.8)',
                'rgba(255, 255, 255, 0.8)',
                'rgba(255, 255, 255, 0.8)',
                'rgba(255, 255, 255, 0.8)',
                'rgba(255, 255, 255, 0.8)',
              ],
              borderColor: [
                'rgba(255, 255, 255, 0.5)',
                'rgba(255, 255, 255, 0.5)',
                'rgba(255, 255, 255, 0.5)',
                'rgba(255, 255, 255, 0.5)',
                'rgba(255, 255, 255, 0.5)',
              ],
              borderWidth: 1
            }]
          },
          options: {
            legend: {
              display: false
            },
            scales: {
              x: {
                display: false
              },
              y: {
                display: false
              },
              xAxes: [{
                display: false
              }],
              yAxes: [{
                display: false,
                ticks: {
                  beginAtZero: true
                }
              }]
            }
          }
        });
      }
    },
    getRole() {
      axiosGetApi('/users/roles', (res) => {
        this.roles = res.data.roles;
      }, () => {
        console.log("finally");
      });
      console.log("executed!!!");
    },
    navigateTo(route) {
      // navigate only if there is a route
      if (route) {
        this.$router.push(route);
      }
    },
    genData() {
      let max = 15000;
      let min = 10;
      for(let gi in this.graphsData){
        this.graphsData[gi].movsQty = Math.floor(Math.random() * (max - min + 1)) + min;
      }
    }
  },
  mounted() {
    this.getRole();
    this.genData();
    this.genGraphs();
    // axiosGet(`${API_URL}/cashflows/maingraph`, (res) => {
    //   this.names = res.data.names;
    //   this.freqs = res.data.freqs;
    //   this.startAt = res.data.startAt;
    //   this.genGraph();
    // });
  }
}
</script>

<style lang="scss" scoped>

.home-actions {
  padding: 10px;

  .v-card {
    //padding: 0px;
    margin-bottom: 15px;

    &.bg-1 {
      background: linear-gradient(45deg, #a52dd8, #e29bf1);
    }

    &.bg-2 {
      background: linear-gradient(45deg, #F6A06A, #f7cf68);
    }

    &.bg-3 {
      background: linear-gradient(45deg, #508090, #7c8ea0)
    }

    &.bg-4 {
      background: linear-gradient(45deg, #16C0A1, #59e0c5);
    }

    &.bg-5 {
      background: linear-gradient(45deg, #FF2270, #ff869a);
    }

    &.bg-6 {
      background: linear-gradient(45deg, #508090, #7c8ea0);
    }

    &.bg-7 {
      background: linear-gradient(45deg, #FFB64D, #ffcb80);
    }

    &.bg-8 {
      background: linear-gradient(45deg,#FF3859,#ff869a);
    }

    &:hover {
      opacity: .9;
    }

    .movs-qty {
      font-size: 1.8rem;
    }

    .data-row{
      padding-right: 0;
    }

    .card-name{
      text-transform: uppercase;
      font-weight: bold;
      //letter-spacing: .010rem;
      font-size: .9rem;
    }

    .v-card__title {
      font-size: .875rem;
      text-transform: uppercase;
    }

    hr {
      border: 1px solid rgba(255, 255, 255, .5);
    }
  }
}

</style>
