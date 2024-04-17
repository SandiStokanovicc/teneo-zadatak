<template>

    <div class="d-flex justify-content-between align-items-center my-3">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="me-2">Kalendar</h2>
            <router-link to="/arhiva">
                <button class="btn btn-primary">Arhiva</button>
            </router-link>
        </div>
        <div style="height: 50px">
            <div v-if="showSuccessAlert" class="alert alert-success" role="alert">
                Uspješno ste registrovali odsustvo!
            </div>
            <div v-if="showArchiveAlert" class="alert alert-success" role="alert">
                Kalendar uspješno arhiviran!
            </div>
            <div v-if="showFailAlert1" class="alert alert-danger" role="alert">
                Nije moguće registrovati godišnji odmor vikendom!
            </div>
            <div v-if="showFailAlert2" class="alert alert-danger" role="alert">
                Nije moguće registrovati više od 7 dana bolovanja!
            </div>
        </div>

        <div>
            <button class="btn btn-info me-2" @click="toArchive()">Sačuvaj u arhivu</button>
            <button class="btn btn-success me-5" data-bs-toggle="modal" data-bs-target="#registrujModal">Registruj
                odsustvo</button>
        </div>

    </div>


    <!-- Modal -->
    <div class="modal fade" id="registrujModal" tabindex="-1" aria-labelledby="registrujModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registrujModalLabel">Registruj odsustvo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="closeModalButton"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="submitForm">
                        <div class="mb-3">
                            <label for="startDate" class="form-label">Datum (Početak)</label>
                            <input type="date" class="form-control" id="startDate" v-model="startDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="endDate" class="form-label">Datum (Kraj)</label>
                            <input type="date" class="form-control" id="endDate" v-model="endDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="absenceType" class="form-label">Vrsta odsustva</label>
                            <select class="form-select" id="absenceType" v-model="absenceType" required>
                                <option disabled value="">Odaberite tip odsustva</option>
                                <option v-for="absenceType in absenceTypes" :key="absenceType.id"
                                    :value="absenceType.id">
                                    {{ absenceType . name }}
                                </option>
                            </select>
                        </div>
                        <div class="modal-footer d-flex">
                            <button type="submit" class="btn btn-primary">Sačuvaj</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <div class="d-flex flex-wrap" :key="renderKey">
        <div v-for="(day, index) in days" :key="index" class="p-2 mb-2 me-2"
            style="width: calc(100% / 7 - 0.5rem); position: relative;">
            <div class="card" :class="{ 'bg-info': day.isWeekend }" style="min-height: 150px;">
                <div class="card-body">
                    <h5 class="card-title"> {{ new Date(day . date) . getDate() }}</h5>

                    <p class="card-text">
                        <div class="card-body">
                            <p v-if="absences.some(absence => absence.date === day.date)">
                                {{ (absences.find(absence => absence.date === day.date) || {}).absence?.name }}
                            </p>
                        </div>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                startDate: '',
                endDate: '',
                absenceType: '',
                absenceTypes: [],
                renderKey: 0,
                showSuccessAlert: false,
                showFailAlert1: false,
                showFailAlert2: false,
                showArchiveAlert: false,
                absences: [],
                days: [],

            };
        },
        methods: {
            async updateAbsences() {
                const getAbsences = await axios.get('http://localhost:8000/api/getAbsences');
                this.absences = [...getAbsences.data];
            },
            async submitForm() {
                if (new Date(this.startDate) > new Date(this.endDate)) {
                    alert('Start date cannot be after end date');
                    return;
                }

                const response = await axios.post('http://localhost:8000/api/insertAbsence', {
                    absence: {
                        start_date: this.startDate,
                        end_date: this.endDate,
                        absence_id: this.absenceType,
                    },
                }, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'Content-Type': 'application/json',
                    },
                });


                if (response.data.success) {
                    this.$refs.closeModalButton.click();
                    this.showSuccessAlert = true;
                    setTimeout(() => {
                        this.showSuccessAlert = false;
                    }, 3000);
                    await this.updateAbsences();
                } else {
                    if (response.data.error === 'weekend') {
                        this.showFailAlert1 = true;
                        setTimeout(() => {
                            this.showFailAlert2 = false;
                        }, 3000);
                    } else if (response.data.error === 'moreThan7Days') {
                        this.showFailAlert2 = true;
                    setTimeout(() => {
                        this.showFailAlert2 = false;
                    }, 3000);
                    }
                    this.showSuccessAlert = false;
                    this.$refs.closeModalButton.click();
                    console.log(response.data.error)
                }
            },


            toArchive: async function() {
                const response = await axios.get('http://localhost:8000/api/toArchive');
                console.log('hit')
                if(response.data.success){
                    this.showArchiveAlert = true;
                    setTimeout(() => {
                        this.showArchiveAlert = false;
                    }, 3000);
                }
            },
        },


        async created() {
            const response = await axios.get('http://localhost:8000/api/getMonthData');
            this.days = response.data;
            const types = await axios.get('http://localhost:8000/api/getAbsenceTypes');
            this.absenceTypes = types.data;
            const getAbsences = await axios.get('http://localhost:8000/api/getAbsences');
            this.absences = getAbsences.data;
        },
    };
</script>
