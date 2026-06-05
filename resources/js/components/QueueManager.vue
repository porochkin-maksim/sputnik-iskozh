<template>
    <div class="queue-manager">
        <div class="card">
            <div class="card-header">
                <h3>Управление очередями</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <button @click="getStatus" class="btn btn-info">Обновить статус</button>
                </div>

                <div v-if="workers.length" class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>PID</th>
                            <th>Очередь</th>
                            <th>Время работы</th>
                            <th>CPU %</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="worker in workers" :key="worker.pid">
                            <td>{{ worker.pid }}</td>
                            <td>{{ worker.queue }}</td>
                            <td>{{ worker.uptime }}</td>
                            <td>{{ worker.cpu }}%</td>
                            <td>
                                <button @click="stopWorker(worker)" class="btn btn-warning btn-sm">Остановить</button>
                                <button @click="clearQueue(worker)"
                                        class="btn btn-danger btn-sm ms-2">Очистить очередь
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else class="alert alert-info">
                    Нет активных воркеров
                </div>

                <div class="mt-3">
                    <div class="form-group">
                        <label for="queue">Очередь:</label>
                        <simple-select v-model="queue"
                                       :options="queueOptions"
                                       id="queue" />
                    </div>
                    <div class="form-group">
                        <label for="tries">Количество попыток:</label>
                        <inline-input v-model="tries"
                                      type="number"
                                      id="tries" />
                    </div>
                    <div class="form-group">
                        <label for="timeout">Таймаут (сек):</label>
                        <inline-input v-model="timeout"
                                      type="number"
                                      id="timeout" />
                    </div>
                    <button @click="startWorker" class="btn btn-success">Запустить воркер</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    onMounted,
    onUnmounted,
    ref,
}                   from 'vue';
import SimpleSelect from '@common/form/SimpleSelect.vue';
import InlineInput  from '@common/form/InlineInput.vue';
import {
    ApiAdminQueueClear,
    ApiAdminQueueStart,
    ApiAdminQueueStatus,
    ApiAdminQueueStop,
}                   from '@api';

const workers      = ref([]);
const queue        = ref('default');
const tries        = ref(3);
const timeout      = ref(60);
const queueOptions = [
    { value: 'high', label: 'Высокий приоритет' },
    { value: 'email', label: 'Email' },
    { value: 'default', label: 'По умолчанию' },
    { value: 'low', label: 'Низкий приоритет' },
];
let refreshTimer   = null;

async function getStatus () {
    try {
        const response = await ApiAdminQueueStatus();
        workers.value  = response.data.workers;
    }
    catch (error) {
        console.error('Ошибка получения статуса:', error);
    }
}

async function startWorker () {
    try {
        await ApiAdminQueueStart({}, {
            queue  : queue.value,
            tries  : tries.value,
            timeout: timeout.value,
        });
        getStatus();
    }
    catch (error) {
        console.error('Ошибка запуска воркера:', error);
    }
}

async function stopWorker (worker) {
    try {
        await ApiAdminQueueStop({}, {
            queue: worker.queue,
        });
        getStatus();
    }
    catch (error) {
        console.error('Ошибка остановки воркера:', error);
    }
}

async function clearQueue (worker) {
    try {
        await ApiAdminQueueClear({}, {
            queue: worker.queue,
        });
        getStatus();
    }
    catch (error) {
        console.error('Ошибка очистки очереди:', error);
    }
}

onMounted(() => {
    getStatus();
    refreshTimer = setInterval(getStatus, 5000);
});

onUnmounted(() => {
    if (refreshTimer) {
        clearInterval(refreshTimer);
    }
});
</script>
