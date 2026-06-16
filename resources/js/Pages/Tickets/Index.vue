<template>
  <div class="min-h-screen bg-gray-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <header class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Sistema Interno de Tickets</h1>
        <p class="mt-2 text-sm text-gray-600">Gerencie e distribua as solicitações de suporte corporativo com eficiência.</p>
      </header>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="bg-white p-6 rounded-lg shadow-sm h-fit">
          <h2 class="text-xl font-semibold text-gray-900 mb-6">Criar Novo Ticket</h2>
          
          <form @submit.prevent="submit">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
              <input 
                v-model="form.title" 
                type="text" 
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                required
              />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
              <textarea 
                v-model="form.description" 
                rows="4" 
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                required
              ></textarea>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Prioridade</label>
              <select 
                v-model="form.priority" 
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
              >
                <option value="low">Baixa</option>
                <option value="medium">Média</option>
                <option value="high">Alta</option>
              </select>
            </div>

            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-1">Modo de Atribuição</label>
              <select 
                v-model="assignmentMode" 
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
              >
                <option value="auto">Atribuição Automática</option>
                <option value="manual">Atribuição Manual</option>
              </select>
            </div>

            <div v-if="assignmentMode === 'manual'" class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-1">Selecionar Atendente</label>
              <select 
                v-model="form.attendant_id" 
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                required
              >
                <option value="">Selecione um operador...</option>
                <option v-for="attendant in attendants" :key="attendant.id" :value="attendant.id">
                  {{ attendant.name }} ({{ attendant.department }})
                </option>
              </select>
            </div>

            <button 
              type="submit" 
              class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors cursor-pointer"
              :disabled="form.processing"
            >
              Enviar Ticket
            </button>
          </form>
        </div>

        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm">
          <div class="sm:flex sm:items-center sm:justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Tabela de Tickets Ativos</h2>
            
            <div class="mt-3 sm:mt-0 sm:ml-4 max-w-xs w-full">
              <input 
                v-model="searchQuery" 
                type="text" 
                placeholder="Buscar por título, descrição ou atendente..." 
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-1.5"
              />
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket / Data</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridade</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Atribuído a</th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="ticket in filteredTickets" :key="ticket.id">
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ ticket.title }}</div>
                    <div class="text-xs text-gray-400 mt-1">Abertura: {{ formatDate(ticket.created_at) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="{
                      'bg-green-100 text-green-800': ticket.priority === 'low',
                      'bg-yellow-100 text-yellow-800': ticket.priority === 'medium',
                      'bg-red-100 text-red-800': ticket.priority === 'high'
                    }" class="px-2.5 py-0.5 rounded-full text-xs font-medium uppercase">
                      {{ ticket.priority === 'low' ? 'Baixa' : ticket.priority === 'medium' ? 'Média' : 'Alta' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusClass(ticket.status)" class="px-2.5 py-0.5 rounded-full text-xs font-medium uppercase">
                      {{ getStatusLabel(ticket.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ ticket.attendant ? ticket.attendant.name : 'Não Atribuído' }}
                  </td>
                 <td class="px-6 py-4 whitespace-nowrap text-center text-xs font-medium">
                <div class="inline-flex rounded-md shadow-sm space-x-2" role="group">
                  <button 
                    @click="viewTicket(ticket)" 
                    class="inline-flex items-center px-3 py-1.5 border border-indigo-200 bg-indigo-50 text-indigo-700 rounded-md hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors cursor-pointer font-semibold uppercase tracking-wider text-[10px]"
                  >
                    Visualizar
                  </button>
                  <button 
                    @click="editTicket(ticket)" 
                    class="inline-flex items-center px-3 py-1.5 border border-emerald-200 bg-emerald-50 text-emerald-700 rounded-md hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer font-semibold uppercase tracking-wider text-[10px]"
                  >
                    Editar
                  </button>
                </div>
              </td>
                </tr>
                <tr v-if="filteredTickets.length === 0">
                  <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                    Nenhum ticket encontrado para a busca realizada.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div v-if="isViewModalOpen" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg max-w-lg w-full p-6 shadow-xl">
        <div class="flex justify-between items-start mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Detalhes do Chamado</h3>
          <button @click="isViewModalOpen = false" class="text-gray-400 hover:text-gray-500 text-2xl line-height-none cursor-pointer">&times;</button>
        </div>
        <div class="space-y-4">
          <div>
            <label class="text-xs font-bold text-gray-400 uppercase">Título</label>
            <p class="text-sm font-semibold text-gray-900">{{ selectedTicket.title }}</p>
          </div>
          <div>
            <label class="text-xs font-bold text-gray-400 uppercase">Descrição</label>
            <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-md border whitespace-pre-wrap">{{ selectedTicket.description }}</p>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-xs font-bold text-gray-400 uppercase">Prioridade</label>
              <p class="text-sm mt-1">
                <span :class="{
                  'bg-green-100 text-green-800': selectedTicket.priority === 'low',
                  'bg-yellow-100 text-yellow-800': selectedTicket.priority === 'medium',
                  'bg-red-100 text-red-800': selectedTicket.priority === 'high'
                }" class="px-2 py-0.5 rounded text-xs font-bold uppercase">
                  {{ selectedTicket.priority === 'low' ? 'Baixa' : selectedTicket.priority === 'medium' ? 'Média' : 'Alta' }}
                </span>
              </p>
            </div>
            <div>
              <label class="text-xs font-bold text-gray-400 uppercase">Status</label>
              <p class="text-sm mt-1">
                <span :class="getStatusClass(selectedTicket.status)" class="px-2 py-0.5 rounded text-xs font-bold uppercase">
                  {{ getStatusLabel(selectedTicket.status) }}
                </span>
              </p>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-xs font-bold text-gray-400 uppercase">Atendente Responsável</label>
              <p class="text-sm text-gray-800 font-medium">{{ selectedTicket.attendant ? selectedTicket.attendant.name : 'Não Atribuído' }}</p>
            </div>
            <div>
              <label class="text-xs font-bold text-gray-400 uppercase">Data/Hora de Abertura</label>
              <p class="text-sm text-gray-800 font-medium">{{ formatDate(selectedTicket.created_at) }}</p>
            </div>
          </div>
        </div>
        <div class="mt-6 flex justify-end">
          <button @click="isViewModalOpen = false" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 text-sm font-medium cursor-pointer">Fechar</button>
        </div>
      </div>
    </div>

    <div v-if="isEditModalOpen" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
        <div class="flex justify-between items-start mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Editar Chamado</h3>
          <button @click="isEditModalOpen = false" class="text-gray-400 hover:text-gray-500 text-2xl line-height-none cursor-pointer">&times;</button>
        </div>
        <form @submit.prevent="updateTicket">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select 
                v-model="editForm.status" 
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
              >
                <option value="open">Aberto</option>
                <option value="in_progress">Em Andamento</option>
                <option value="resolved">Resolvido</option>
                <option value="closed">Fechado</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Atendente Responsável</label>
              <select 
                v-model="editForm.attendant_id" 
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
              >
                <option :value="null">Sem Atendente (Não Atribuído)</option>
                <option v-for="attendant in attendants" :key="attendant.id" :value="attendant.id">
                  {{ attendant.name }} ({{ attendant.department }})
                </option>
              </select>
            </div>
          </div>
          <div class="mt-6 flex justify-end space-x-3">
            <button type="button" @click="isEditModalOpen = false" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 text-sm font-medium cursor-pointer">Cancelar</button>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm font-medium cursor-pointer" :disabled="editForm.processing">Salvar Alterações</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  tickets: Array,
  attendants: Array
});

const assignmentMode = ref('auto');
const searchQuery = ref('');

const form = useForm({
  title: '',
  description: '',
  priority: 'low',
  attendant_id: null,
  assignment_mode: 'auto'
});

watch(assignmentMode, (newMode) => {
  form.assignment_mode = newMode;
  if (newMode === 'auto') {
    form.attendant_id = null;
  }
});

// CLIENT-SIDE REALTIME FILTER
const filteredTickets = computed(() => {
  if (!searchQuery.value.trim()) {
    return props.tickets;
  }
  
  const query = searchQuery.value.toLowerCase().trim();
  
  return props.tickets.filter(ticket => {
    const titleMatch = ticket.title?.toLowerCase().includes(query);
    const descriptionMatch = ticket.description?.toLowerCase().includes(query);
    const attendantMatch = ticket.attendant?.name?.toLowerCase().includes(query);
    
    return titleMatch || descriptionMatch || attendantMatch;
  });
});

const submit = () => {
  form.post('/tickets', {
    onSuccess: () => {
      form.reset('title', 'description', 'priority', 'attendant_id');
    }
  });
};

const isViewModalOpen = ref(false);
const isEditModalOpen = ref(false);
const selectedTicket = ref(null);

const editForm = useForm({
  status: '',
  attendant_id: null
});

const viewTicket = (ticket) => {
  selectedTicket.value = ticket;
  isViewModalOpen.value = true;
};

const editTicket = (ticket) => {
  selectedTicket.value = ticket;
  editForm.status = ticket.status;
  editForm.attendant_id = ticket.attendant_id;
  isEditModalOpen.value = true;
};

const updateTicket = () => {
  editForm.put(`/tickets/${selectedTicket.value.id}`, {
    onSuccess: () => {
      isEditModalOpen.value = false;
    }
  });
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const getStatusLabel = (status) => {
  const labels = {
    open: 'Aberto',
    in_progress: 'Em Andamento',
    resolved: 'Resolvido',
    closed: 'Fechado'
  };
  return labels[status] || status;
};

const getStatusClass = (status) => {
  const classes = {
    open: 'bg-blue-100 text-blue-800',
    in_progress: 'bg-orange-100 text-orange-800',
    resolved: 'bg-green-100 text-green-800',
    closed: 'bg-gray-100 text-gray-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};
</script>