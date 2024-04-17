<template>
    <div class="container mb-3">
        <div class="row">
            <div class="d-flex justify-content-between align-items-center">
            <h1 class="my-3">Arhiva odsustava</h1>
            <router-link to="/calendar" class="btn btn-primary">Kalendar</router-link>
            </div>
        </div>
        <div class="row" v-for="archive_number in Object.keys(archives)" :key="archive_number">
            <div class="col-12">
                <h2 class="my-3">Arhiva {{ archive_number }} - {{ formatDate(archives[archive_number][0].created_at) }}</h2>
                <ul class="list-group">
                    <li class="list-group-item" v-for="(record, index) in archives[archive_number]" :key="index">
                        <p>{{ formatRecordDate(record.date) }} - {{ record.absence.name }}</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                archives: {},
                record: '',
                created_at: '',
            };
        },
        methods: {
    formatDate(dateString) {
            const date = new Date(dateString);
            const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
            const formattedDate = date.toLocaleDateString(undefined, dateOptions);
            const formattedTime = date.toLocaleTimeString(undefined, timeOptions);
            return `${formattedDate} u ${formattedTime}`;
        },
            formatRecordDate(dateString) {
        const date = new Date(dateString);
        const day = date.getDate();
        const month = date.getMonth() + 1; // Months are zero-based
        const year = date.getFullYear();
        return `${day}-${month}-${year}`;
    },
    },


        async created() {
            const response = await axios.get('http://localhost:8000/api/getArchive');
            const records = response.data;

            this.archives = records.reduce((grouped, record) => {
                (grouped[record.archive_number] = grouped[record.archive_number] || []).push(record);
                return grouped;
            }, {});

            for (const archive_number in this.archives) {
                if (this.archives.hasOwnProperty(archive_number)) {
                    const group = this.archives[archive_number];
                    if (group.length > 0 && !this.created_at) {
                        this.created_at = group[0].created_at;
                    }
                }
            }
        },
    };
</script>
