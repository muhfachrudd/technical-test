<script setup>
import { reactive } from 'vue';
import { X } from 'lucide-vue-next';
import { STATUS_OPTIONS, PRIORITY_OPTIONS, TYPE_OPTIONS } from '../utils/constants';

const emit = defineEmits(['close', 'save']);

const statusOptions = STATUS_OPTIONS;
const priorityOptions = PRIORITY_OPTIONS;
const typeOptions = TYPE_OPTIONS;

const form = reactive({
  task: '',
  developer: '',
  status: statusOptions[0].name,
  priority: priorityOptions[2].name, // Medium
  type: typeOptions[0].name,
  date: new Date().toISOString().split('T')[0],
  estimated_sp: 0,
  actual_sp: 0
});

const save = () => {
  if (!form.task) return;
  emit('save', { ...form });
};
</script>

<template>
  <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-[#1E1E2D] w-full max-w-lg rounded-xl shadow-2xl border border-gray-700 overflow-hidden animate-in fade-in zoom-in duration-200">
      <div class="p-6 flex items-center justify-between border-b border-gray-800">
        <h2 class="text-lg font-semibold flex items-center gap-2">
          Create New Task
        </h2>
        <button @click="$emit('close')" class="text-gray-500 hover:text-white transition-colors">
          <X class="w-5 h-5" />
        </button>
      </div>

      <form @submit.prevent="save" class="p-6 space-y-4">
        <div>
          <label class="block text-xs text-gray-400 mb-1.5 uppercase font-bold tracking-wider">Task Name</label>
          <input 
            v-model="form.task" 
            type="text" 
            placeholder="What needs to be done?"
            class="w-full bg-[#151521] border border-gray-700 rounded-lg px-4 py-2.5 text-sm focus:border-blue-500 transition-colors"
            required
          >
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs text-gray-400 mb-1.5 uppercase font-bold tracking-wider">Developer</label>
            <input 
              v-model="form.developer" 
              type="text" 
              placeholder="Assignee"
              class="w-full bg-[#151521] border border-gray-700 rounded-lg px-4 py-2.5 text-sm focus:border-blue-500 transition-colors"
            >
          </div>
          <div>
            <label class="block text-xs text-gray-400 mb-1.5 uppercase font-bold tracking-wider">Date</label>
            <input 
              v-model="form.date" 
              type="date" 
              class="w-full bg-[#151521] border border-gray-700 rounded-lg px-4 py-2.5 text-sm focus:border-blue-500 transition-colors"
            >
          </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
          <div>
            <label class="block text-xs text-gray-400 mb-1.5 uppercase font-bold tracking-wider">Status</label>
            <select v-model="form.status" class="w-full bg-[#151521] border border-gray-700 rounded-lg px-3 py-2.5 text-sm outline-none">
                <option v-for="opt in statusOptions" :key="opt.name" :value="opt.name">
                  {{ opt.name }}
                </option>
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-400 mb-1.5 uppercase font-bold tracking-wider">Priority</label>
            <select v-model="form.priority" class="w-full bg-[#151521] border border-gray-700 rounded-lg px-3 py-2.5 text-sm outline-none">
                <option v-for="opt in priorityOptions" :key="opt.name" :value="opt.name">
                  {{ opt.name }}
                </option>
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-400 mb-1.5 uppercase font-bold tracking-wider">Type</label>
            <select v-model="form.type" class="w-full bg-[#151521] border border-gray-700 rounded-lg px-3 py-2.5 text-sm outline-none">
                <option v-for="opt in typeOptions" :key="opt.name" :value="opt.name">
                  {{ opt.name }}
                </option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs text-gray-400 mb-1.5 uppercase font-bold tracking-wider">Estimated SP</label>
            <input 
              v-model.number="form.estimated_sp" 
              type="number" 
              class="w-full bg-[#151521] border border-gray-700 rounded-lg px-4 py-2.5 text-sm"
            >
          </div>
          <div>
            <label class="block text-xs text-gray-400 mb-1.5 uppercase font-bold tracking-wider">Actual SP</label>
            <input 
              v-model.number="form.actual_sp" 
              type="number" 
              class="w-full bg-[#151521] border border-gray-700 rounded-lg px-4 py-2.5 text-sm"
            >
          </div>
        </div>

        <div class="pt-6 flex gap-3">
          <button 
            type="button"
            @click="$emit('close')"
            class="flex-1 px-4 py-2.5 rounded-lg border border-gray-700 text-sm hover:bg-white/5 transition-colors"
          >
            Cancel
          </button>
          <button 
            type="submit"
            class="flex-1 px-4 py-2.5 rounded-lg bg-blue-600 text-sm hover:bg-blue-700 transition-colors font-semibold"
          >
            Create Task
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
