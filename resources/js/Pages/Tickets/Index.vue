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
              class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
              :disabled="form.processing"
            >
              Enviar Ticket
            </button>
          </form>
        </div>

        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm">
          <h2 class="text-xl font-semibold text-gray-900 mb-6">Tabela de Tickets Ativos</h2>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridade</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Atribuído a</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="ticket in tickets" :key="ticket.id">
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ ticket.title }}</div>
                    <div class="text-sm text-gray-500 max-w-xs truncate">{{ ticket.description }}</div>
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
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 uppercase">
                      {{ ticket.status === 'open' ? 'Aberto' : 'Fechado' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ ticket.attendant ? ticket.attendant.name : 'Não Atribuído' }}
                  </td>
                </tr>
                <tr v-if="tickets.length === 0">
                  <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                    Nenhum ticket encontrado. Crie um para começar.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  tickets: Array,
  attendants: Array
});

const assignmentMode = ref('auto');

const form = useForm({
  title: '',
  description: '',
  priority: 'low',
  attendant_id: null
});

watch(assignmentMode, (newMode) => {
  if (newMode === 'auto') {
    form.attendant_id = null;
  }
});

const submit = () => {
  form.post('/tickets', {
    onSuccess: () => form.reset('title', 'description', 'priority', 'attendant_id')
  });
};
</script>