<script setup>
import { computed } from 'vue';
import { format } from 'date-fns';
import { PlusCircle, MessageSquare, Info } from 'lucide-vue-next';

const props = defineProps(['tasks']);
const emit = defineEmits(['update']);

const statusOptions = [
  { name: 'Ready to start', color: '#2563EB' },
  { name: 'In Progress', color: '#F59E0B' },
  { name: 'Waiting for review', color: '#06B6D4' },
  { name: 'Pending Deploy', color: '#8B5CF6' },
  { name: 'Done', color: '#10B981' },
  { name: 'Stuck', color: '#EF4444' }
];

const priorityOptions = [
  { name: 'Critical', color: '#7F1D1D' },
  { name: 'High', color: '#B91C1C' },
  { name: 'Medium', color: '#3B82F6' },
  { name: 'Low', color: '#6B7280' },
  { name: 'Best Effort', color: '#4B5563' }
];

const typeOptions = [
  { name: 'Feature Enhancements', color: '#EC4899' },
  { name: 'Other', color: '#6366F1' },
  { name: 'Bug', color: '#F43F5E' }
];

const updateField = (task, field, value) => {
  emit('update', { ...task, [field]: value });
};

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  return format(new Date(dateStr), 'dd MMM, yyyy');
};

const getStatusColor = (name) => statusOptions.find(o => o.name === name)?.color + '33';
const getStatusTextColor = (name) => statusOptions.find(o => o.name === name)?.color;

const calculatePercentage = (field, list) => {
  if (!props.tasks.length) return [];
  const counts = {};
  props.tasks.forEach(t => {
    counts[t[field]] = (counts[t[field]] || 0) + 1;
  });
  
  return list.map(opt => ({
    ...opt,
    percent: Math.round(((counts[opt.name] || 0) / props.tasks.length) * 100)
  })).filter(o => o.percent > 0);
};

const statusStats = computed(() => calculatePercentage('status', statusOptions));
const priorityStats = computed(() => calculatePercentage('priority', priorityOptions));
const typeStats = computed(() => calculatePercentage('type', typeOptions));

const totalEstimated = computed(() => props.tasks.reduce((sum, t) => sum + (Number(t.estimated_sp) || 0), 0));
const totalActual = computed(() => props.tasks.reduce((sum, t) => sum + (Number(t.actual_sp) || 0), 0));

</script>

<template>
  <div class="overflow-x-auto glass-effect rounded-lg shadow-2xl">
    <table class="w-full text-left border-collapse min-w-[1000px]">
      <thead>
        <tr class="text-gray-400 text-xs border-b border-gray-700">
          <th class="p-4 w-12"><input type="checkbox" class="rounded bg-gray-700 border-none"></th>
          <th class="p-4 font-normal">Task</th>
          <th class="p-4 font-normal">Developer</th>
          <th class="p-4 font-normal">Status</th>
          <th class="p-4 font-normal">Priority</th>
          <th class="p-4 font-normal">Type</th>
          <th class="p-4 font-normal">Date</th>
          <th class="p-4 font-normal flex items-center gap-1">Estimated SP <Info class="w-3 h-3" /></th>
          <th class="p-4 font-normal flex items-center gap-1">Actual SP <Info class="w-3 h-3" /></th>
          <th class="p-4 w-10 text-center"><PlusCircle class="w-4 h-4 mx-auto" /></th>
        </tr>
      </thead>
      <tbody class="text-sm">
        <tr v-for="task in tasks" :key="task.id" class="border-b border-gray-800 hover:bg-white/5 transition-colors group">
          <td class="p-4"><input type="checkbox" class="rounded bg-gray-700 border-none"></td>
          
          <td class="p-4 min-w-[200px]">
            <div class="flex items-center gap-3">
              <input 
                type="text" 
                :value="task.task"
                @change="e => updateField(task, 'task', e.target.value)"
                class="w-full focus:bg-gray-800/50 rounded px-1"
              >
              <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <MessageSquare class="w-4 h-4 text-gray-500 cursor-pointer" />
              </div>
            </div>
          </td>

          <td class="p-4">
            <div class="flex -space-x-2">
              <div class="w-8 h-8 rounded-full border-2 border-gray-900 bg-gray-700 flex items-center justify-center text-[10px] font-bold">
                {{ task.developer?.[0]?.toUpperCase() || '?' }}
              </div>
            </div>
          </td>

          <td class="p-2">
            <select 
              :value="task.status"
              @change="e => updateField(task, 'status', e.target.value)"
              class="w-full py-1.5 px-3 rounded text-[11px] font-medium"
              :style="{ backgroundColor: getStatusColor(task.status), color: getStatusTextColor(task.status) }"
            >
              <option v-for="opt in statusOptions" :key="opt.name" :value="opt.name" class="bg-gray-800 text-white">
                {{ opt.name }}
              </option>
            </select>
          </td>

          <td class="p-2">
             <select 
              :value="task.priority"
              @change="e => updateField(task, 'priority', e.target.value)"
              class="w-full py-1.5 px-3 rounded text-[11px] font-medium"
              :style="{ backgroundColor: priorityOptions.find(o => o.name === task.priority)?.color + '33', color: priorityOptions.find(o => o.name === task.priority)?.color }"
            >
              <option v-for="opt in priorityOptions" :key="opt.name" :value="opt.name" class="bg-gray-800 text-white">
                {{ opt.name }}
              </option>
            </select>
          </td>

          <td class="p-2">
            <select 
              :value="task.type"
              @change="e => updateField(task, 'type', e.target.value)"
              class="w-full py-1.5 px-3 rounded text-[11px] font-medium whitespace-nowrap overflow-hidden text-ellipsis"
              :style="{ backgroundColor: typeOptions.find(o => o.name === task.type)?.color + '33', color: typeOptions.find(o => o.name === task.type)?.color }"
            >
              <option v-for="opt in typeOptions" :key="opt.name" :value="opt.name" class="bg-gray-800 text-white">
                {{ opt.name }}
              </option>
            </select>
          </td>

          <td class="p-4 text-gray-400">
            <input 
              type="date" 
              :value="task.date"
              @change="e => updateField(task, 'date', e.target.value)"
              class="w-full text-[12px]"
            >
          </td>

          <td class="p-4">
            <div class="flex items-center gap-1">
              <input 
                type="number" 
                :value="task.estimated_sp"
                @change="e => updateField(task, 'estimated_sp', parseInt(e.target.value))"
                class="w-12 text-center"
              >
              <span class="text-gray-500 text-[10px]">SP</span>
            </div>
          </td>

          <td class="p-4 border-r border-gray-800">
             <div class="flex items-center gap-1">
              <input 
                type="text" 
                :value="task.actual_sp"
                @change="e => updateField(task, 'actual_sp', parseFloat(e.target.value))"
                class="w-12 text-center"
              >
              <span class="text-gray-500 text-[10px]">SP</span>
            </div>
          </td>
          
          <td class="p-4"></td>
        </tr>
      </tbody>
      
      <!-- Summary Row -->
      <tfoot>
        <tr class="bg-gray-900/30">
          <td class="p-4"></td>
          <td class="p-4"></td>
          <td class="p-4"></td>
          
          <td class="p-2">
            <div class="flex h-2 rounded-full overflow-hidden bg-gray-800">
              <div 
                v-for="stat in statusStats" 
                :key="stat.name"
                :style="{ width: stat.percent + '%', backgroundColor: stat.color }"
                class="h-full"
                :title="stat.name + ': ' + stat.percent + '%'"
              ></div>
            </div>
          </td>

          <td class="p-2">
            <div class="flex h-2 rounded-full overflow-hidden bg-gray-800">
              <div 
                v-for="stat in priorityStats" 
                :key="stat.name"
                :style="{ width: stat.percent + '%', backgroundColor: stat.color }"
                class="h-full"
              ></div>
            </div>
          </td>

          <td class="p-2">
            <div class="flex h-2 rounded-full overflow-hidden bg-gray-800">
              <div 
                v-for="stat in typeStats" 
                :key="stat.name"
                :style="{ width: stat.percent + '%', backgroundColor: stat.color }"
                class="h-full"
              ></div>
            </div>
          </td>

          <td class="p-4"></td>
          
          <td class="p-4 text-center">
            <span class="text-gray-400 text-xs">{{ totalEstimated }} SP</span>
            <div class="text-[9px] text-gray-600">sum</div>
          </td>

          <td class="p-4 text-center border-r border-gray-800">
             <span class="text-gray-400 text-xs">{{ totalActual }} SP</span>
             <div class="text-[9px] text-gray-600">sum</div>
          </td>
          
          <td></td>
        </tr>
      </tfoot>
    </table>
  </div>
</template>
