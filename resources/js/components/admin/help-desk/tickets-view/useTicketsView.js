import {
    computed,
    onMounted,
    ref,
}                           from 'vue';
import { usePermissions }   from '@composables/usePermissions.js';
import { useResponseError } from '@composables/useResponseError';
import { useFormat }        from '@composables/useFormat';
import {
    ApiAdminHelpDeskTicketsDelete,
    ApiAdminHelpDeskTicketsSave,
    ApiFilesDelete,
}                           from '@api';
import { routeUri }         from '@utils/routeUri.js';
import { TicketStatusEnum } from '@utils/enum.js';

export function useTicketsView (props) {
    const { errors, parseResponseErrors, showSuccess, showDanger, clearError } = useResponseError();
    const { formatDate }                                                       = useFormat();
    const { has }                                                              = usePermissions();

    const saving              = ref(false);
    const ticketData          = ref(null);
    const existingFiles       = ref([]);
    const existingResultFiles = ref([]);
    const newTicketFiles      = ref([]);
    const newResultFiles      = ref([]);
    const ticketFilesPanel    = ref(null);
    const resultFilesPanel    = ref(null);

    const canEdit = computed(() =>
        has('help_desk', 'edit') &&
        ![TicketStatusEnum.CLOSED.value, TicketStatusEnum.REJECTED.value].includes(ticketData.value?.status),
    );

    const canDelete = computed(() =>
        has('help_desk', 'drop') &&
        ![TicketStatusEnum.CLOSED.value, TicketStatusEnum.REJECTED.value].includes(ticketData.value?.status),
    );

    const editForm = ref({
        description  : '',
        result       : '',
        type         : null,
        category_id  : null,
        service_id   : null,
        priority     : null,
        status       : null,
        contact_name : '',
        contact_phone: '',
        contact_email: '',
        user_id      : null,
        account_id   : null,
    });

    const categoryOptions = computed(() => [
        { value: null, label: 'Не выбрано' },
        ...props.categories.map(c => ({ value: c.id, label: c.name })),
    ]);

    const serviceOptions = computed(() => {
        let list = props.services;
        if (editForm.value.category_id) {
            list = list.filter(s => s.category_id === editForm.value.category_id);
        }

        return [
            { value: null, label: 'Не выбрано' },
            ...list.map(s => ({ value: s.id, label: s.name })),
        ];
    });

    const typeOptions     = computed(() => props.types.map(s => ({ value: s.value, label: s.label })));
    const statusOptions   = computed(() => props.statuses.map(s => ({ value: s.value, label: s.label })));
    const priorityOptions = computed(() => props.priorities.map(p => ({ value: p.value, label: p.label })));
    const userOptions     = computed(() => [
        { value: null, label: 'Не выбран' },
        ...props.users.map(u => ({ value: u.id, label: u.fullName })),
    ]);

    const initData = () => {
        ticketData.value          = props.ticket;
        existingFiles.value       = props.ticket?.files || [];
        existingResultFiles.value = props.ticket?.result_files || [];
        editForm.value            = {
            description  : props.ticket.description || '',
            result       : props.ticket.result || '',
            type         : props.ticket.type,
            category_id  : props.ticket.category_id,
            service_id   : props.ticket.service_id,
            priority     : props.ticket.priority,
            status       : props.ticket.status,
            contact_name : props.ticket.contact_name || '',
            contact_phone: props.ticket.contact_phone || '',
            contact_email: props.ticket.contact_email || '',
            user_id      : props.ticket.user_id,
            account_id   : props.ticket.account_id,
        };
    };

    const onTypeChange = () => {
        editForm.value.category_id = null;
        editForm.value.service_id  = null;
        clearError('type');
    };

    const onCategoryChange = () => {
        editForm.value.service_id = null;
        clearError('service_id');
    };

    const onTicketFilesUpdate = (files) => {
        newTicketFiles.value = files;
    };

    const onResultFilesUpdate = (files) => {
        newResultFiles.value = files;
    };

    const deleteTicketFile = async (file) => {
        if (!confirm('Удалить файл?')) {
            return;
        }

        try {
            await ApiFilesDelete(file.id);
            existingFiles.value = existingFiles.value.filter(f => f.id !== file.id);
            showSuccess('Файл удалён');
        }
        catch (error) {
            parseResponseErrors(error);
        }
    };

    const deleteResultFile = async (file) => {
        if (!confirm('Удалить файл?')) {
            return;
        }

        try {
            await ApiFilesDelete(file.id);
            existingResultFiles.value = existingResultFiles.value.filter(f => f.id !== file.id);
            showSuccess('Файл удалён');
        }
        catch (error) {
            parseResponseErrors(error);
        }
    };

    const saveTicket = async () => {
        if (!canEdit.value) {
            return;
        }

        saving.value = true;
        errors.value = {};

        try {
            const formData = new FormData();
            formData.append('id', ticketData.value.id);
            formData.append('description', editForm.value.description || '');
            formData.append('result', editForm.value.result || '');
            formData.append('type', editForm.value.type);
            formData.append('category_id', editForm.value.category_id || '');
            formData.append('service_id', editForm.value.service_id || '');
            formData.append('priority', editForm.value.priority);
            formData.append('status', editForm.value.status);
            formData.append('contact_name', editForm.value.contact_name || '');
            formData.append('contact_phone', editForm.value.contact_phone || '');
            formData.append('contact_email', editForm.value.contact_email || '');
            formData.append('user_id', editForm.value.user_id || '');
            formData.append('account_id', editForm.value.account_id || '');

            for (const file of newTicketFiles.value) {
                formData.append('files[]', file);
            }

            for (const file of newResultFiles.value) {
                formData.append('result_files[]', file);
            }

            const response            = await ApiAdminHelpDeskTicketsSave({}, formData);
            ticketData.value          = response.data.ticket;
            existingFiles.value       = response.data.ticket?.files || [];
            existingResultFiles.value = response.data.ticket?.result_files || [];

            newTicketFiles.value = [];
            newResultFiles.value = [];
            ticketFilesPanel.value?.clearNewFiles();
            resultFilesPanel.value?.clearNewFiles();

            showSuccess('Заявка сохранена');
        }
        catch (error) {
            parseResponseErrors(error);
        }
        finally {
            saving.value = false;
        }
    };

    const deleteTicket = async () => {
        if (!canDelete.value) {
            return;
        }

        if (!confirm('Удалить заявку?')) {
            return;
        }

        try {
            await ApiAdminHelpDeskTicketsDelete(ticketData.value.id);
            showSuccess('Заявка удалена');
            window.location.href = routeUri('adminHelpDeskIndex');
        }
        catch (error) {
            showDanger('Ошибка удаления');
            parseResponseErrors(error);
        }
    };

    onMounted(() => {
        initData();
    });

    return {
        canDelete,
        canEdit,
        categoryOptions,
        clearError,
        deleteResultFile,
        deleteTicket,
        deleteTicketFile,
        editForm,
        errors,
        existingFiles,
        existingResultFiles,
        formatDate,
        initData,
        newResultFiles,
        newTicketFiles,
        onCategoryChange,
        onResultFilesUpdate,
        onTicketFilesUpdate,
        onTypeChange,
        priorityOptions,
        resultFilesPanel,
        saveTicket,
        saving,
        serviceOptions,
        statusOptions,
        ticketData,
        ticketFilesPanel,
        typeOptions,
        userOptions,
    };
}
