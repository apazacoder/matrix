<template>
    <div id="main">
        <div class="title-section">
            <h1>Permisos</h1>
        </div>
        <v-card>
            <v-card-text>
                <actions @evActionsModified="actionsModifiedHandler"></actions>
            </v-card-text>
            <v-card-text>
                <resources @evResourcesModified="resourcesModifiedHandler"></resources>
            </v-card-text>
            <v-card-text>
                <permissions :updateSignal="updateSignal"></permissions>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    import Resources from './../components/Resources.vue';
    import Actions from './../components/Actions.vue';
    import Permissions from './../components/Permissions.vue';

    export default {
        name: "Permissions",
        components: {
            'resources': Resources,
            'actions': Actions,
            'permissions': Permissions
        },
        data: () => ({
            updateSignal: 0
        }),
        methods: {
            actionsModifiedHandler() {
                this.sendUpdateSignal();
            },
            resourcesModifiedHandler(param) {
                this.sendUpdateSignal();
            },
            sendUpdateSignal() {
                this.updateSignal++;
            }
        }
    }
</script>

<style scoped>

</style>
