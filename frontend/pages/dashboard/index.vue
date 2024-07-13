<template>
    <page-home page-name="system-dashboard">
        <template v-slot:default="{ pulse: { slow_query } }">
            <v-card-text v-if="slow_query && slow_query.data">
                <v-sheet class="overflow-hidden" rounded="lg">
                    <v-toolbar density="compact">
                        <v-toolbar-title
                            class="text-caption text-grey text-uppercase"
                            >Slow Query</v-toolbar-title
                        >
                    </v-toolbar>
                    <v-divider></v-divider>

                    <v-sheet>
                        <v-card-text>
                            <v-sheet
                                class="mb-2"
                                color="grey-lighten-4"
                                v-for="(query, index) in slow_query.data"
                                :key="index"
                            >
                                <div class="text-caption">{{ query.sql }}</div>
                            </v-sheet>
                        </v-card-text>
                    </v-sheet>
                </v-sheet>
            </v-card-text>
        </template>
    </page-home>
</template>

<script>
export default {
    name: "system-dashboard",
};
</script>
