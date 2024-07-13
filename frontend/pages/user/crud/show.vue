<template>
    <form-show with-helpdesk with-activity-logs>
        <template v-slot:default="{ record }">
            <v-text-field v-model="record.name"></v-text-field>
        </template>
    </form-show>
</template>

<script>
export default {
    name: "system-user-show",
};
</script>
