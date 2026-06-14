<template>
    <loading-spinner
        v-if="loading"
        size="lg"
        color="primary"
        text="Загрузка заявок..."
        wrapper-class="py-5"
    />

    <template v-else>
        <div v-if="tickets.length === 0" class="profile-empty-state">
            <hr>
            <h6 class="text-center text-secondary m-0"><i>заявок нет...</i></h6>
        </div>

        <template v-else>
            <div class="profile-tickets-list">
                <div v-for="ticket in tickets" :key="ticket.id" class="page-card profile-ticket-card">
                    <div class="profile-ticket-card__header">
                        <div class="profile-ticket-card__title">
                            <span class="profile-ticket-card__label">№{{ ticket.id }}</span>
                            <span class="profile-ticket-card__value">{{ ticket.created_at }}</span>
                        </div>
                        <span class="badge" :class="'badge-' + statusBadge(ticket.status)">{{ ticket.status_name }}</span>
                    </div>
                    <div class="profile-ticket-card__meta">
                        <div class="profile-ticket-chip">
                            <span class="profile-ticket-chip__label">Тип</span>
                            <span class="profile-ticket-chip__value">
                                <i :class="ticket.type_icon"></i> {{ ticket.type_name }}
                            </span>
                        </div>
                        <div class="profile-ticket-chip">
                            <span class="profile-ticket-chip__label">Категория</span>
                            <span class="profile-ticket-chip__value">{{ ticket.category_name }}</span>
                        </div>
                    </div>
                    <div class="profile-ticket-card__action">
                        <a :href="'/home/help-desk/' + ticket.id" class="btn btn-sm btn-outline-primary">
                            Подробнее <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div v-if="hasMore" class="text-center mt-3">
                <button
                    class="btn btn-outline-primary btn-sm"
                    :disabled="loadingMore"
                    @click="loadMore"
                >
                    <i v-if="loadingMore" class="fa fa-spinner fa-spin me-1"></i>
                    Показать ещё
                </button>
            </div>
        </template>
    </template>
</template>

<script setup>
import { ref, computed } from 'vue';
import LoadingSpinner from '@common/LoadingSpinner.vue';
import { useResponseError } from '@composables/useResponseError';
import { ApiProfileHelpDeskList } from '@api';

const { parseResponseErrors } = useResponseError();

const loading     = ref(true);
const loadingMore = ref(false);
const tickets     = ref([]);
const total       = ref(0);
const limit       = ref(20);
const offset      = ref(0);

const hasMore = computed(() => tickets.value.length < total.value);

function statusBadge(status) {
    const map = {
        1: 'warning',
        2: 'info',
        3: 'primary',
        4: 'secondary',
        5: 'danger',
    };
    return map[status] || 'secondary';
}

async function loadData() {
    loading.value = true;
    try {
        const response = await ApiProfileHelpDeskList({}, {
            limit: limit.value,
            skip : 0,
        });
        tickets.value = response.data.tickets;
        total.value   = response.data.total;
        limit.value   = response.data.limit || 20;
        offset.value  = tickets.value.length;
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
    }
}

async function loadMore() {
    loadingMore.value = true;
    try {
        const response = await ApiProfileHelpDeskList({}, {
            limit: limit.value,
            skip : offset.value,
        });
        tickets.value = [...tickets.value, ...response.data.tickets];
        offset.value  = tickets.value.length;
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        loadingMore.value = false;
    }
}

loadData();
</script>
