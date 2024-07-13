<template>
    <form-edit>
        <template v-slot:default="{ record }">
            <v-text-field v-model="record.name"></v-text-field>
        </template>
    </form-edit>
</template>

<script>
export default {
    name: "system-user-edit",
};
</script>
