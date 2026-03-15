<script setup>
import { computed } from 'vue';
import { MessageSquare, PlusCircle, User, Info, ChevronDown } from 'lucide-vue-next';
import { format } from 'date-fns';
import { STATUS_OPTIONS, PRIORITY_OPTIONS, TYPE_OPTIONS } from '../utils/constants';

const props = defineProps(['tasks', 'sortConfig']);
const emit = defineEmits(['update', 'sort']);

const getSortIcon = (key) => {
  const item = props.sortConfig?.find(s => s.key === key);
  if (!item) return '';
  return item.order === 'asc' ? ' ↑' : ' ↓';
};

const statusOptions = STATUS_OPTIONS;
const priorityOptions = PRIORITY_OPTIONS;
const typeOptions = TYPE_OPTIONS;

const updateField = (task, field, value) => {
  emit('update', { ...task, [field]: value });
};

const getStatusStyle = (name) => {
  const opt = statusOptions.find(o => o.name === name);
  return opt ? { backgroundColor: opt.color, color: opt.textColor } : { backgroundColor: '#3A3F50' };
};

const getPriorityStyle = (name) => {
  const opt = priorityOptions.find(o => o.name === name);
  return opt ? { backgroundColor: opt.color, color: '#FFFFFF' } : { backgroundColor: '#3A3F50' };
};

const getTypeStyle = (name) => {
  const opt = typeOptions.find(o => o.name === name);
  return opt ? { backgroundColor: opt.color, color: opt.textColor } : { backgroundColor: '#3A3F50' };
};

const calculateStats = (field, options) => {
  if (!props.tasks.length) return [];
  const counts = {};
  props.tasks.forEach(t => { 
    if (t[field]) counts[t[field]] = (counts[t[field]] || 0) + 1; 
  });
  return options.map(opt => ({
    ...opt,
    percent: (counts[opt.name] || 0) / props.tasks.length * 100
  })).filter(o => o.percent > 0);
};

const statusStats = computed(() => calculateStats('status', statusOptions));
const priorityStats = computed(() => calculateStats('priority', priorityOptions));
const typeStats = computed(() => calculateStats('type', typeOptions));

const totalEstimated = computed(() => props.tasks.reduce((sum, t) => sum + (Number(t.estimated_sp) || 0), 0));
const totalActual = computed(() => props.tasks.reduce((sum, t) => sum + (Number(t.actual_sp) || 0), 0));

const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  try {
    return format(new Date(dateStr), "dd MMM, yyyy");
  } catch (e) {
    return dateStr;
  }
};
</script>

<template>
  <div class="p-6">
    <!-- Group Header -->
    <div class="flex items-center gap-2 mb-4 text-[#C084FC]">
      <ChevronDown class="w-5 h-5" />
      <h2 class="text-lg font-semibold">All Task</h2>
    </div>

    <!-- Table Container -->
    <div class="overflow-x-auto rounded border border-[#232333] bg-[#141420]">
      <table class="w-full table-fixed text-[13px] border-collapse">
        <!-- HEADER -->
        <thead class="text-[#8E8E9F] bg-[#16161F]">
          <tr class="h-10">
            <th class="w-1 border-r border-b border-[#232333]">
              <!-- Left indicator space -->
            </th>
            <th class="w-10 border-r border-b border-[#232333] text-center">
              <input type="checkbox" class="rounded border-gray-700 bg-gray-800">
            </th>
            <th @click="emit('sort', 'task')" class="w-[300px] border-r border-b border-[#232333] text-left px-4 font-normal cursor-pointer hover:bg-white/5">
              Task {{ getSortIcon('task') }}
            </th>
            <th @click="emit('sort', 'developer')" class="w-[100px] border-r border-b border-[#232333] text-center font-normal cursor-pointer hover:bg-white/5">
              Developer {{ getSortIcon('developer') }}
            </th>
            <th @click="emit('sort', 'status')" class="w-[140px] border-r border-b border-[#232333] text-center font-normal cursor-pointer hover:bg-white/5">
              Status {{ getSortIcon('status') }}
            </th>
            <th @click="emit('sort', 'priority')" class="w-[140px] border-r border-b border-[#232333] text-center font-normal cursor-pointer hover:bg-white/5">
              Priority {{ getSortIcon('priority') }}
            </th>
            <th @click="emit('sort', 'type')" class="w-[140px] border-r border-b border-[#232333] text-center font-normal cursor-pointer hover:bg-white/5">
              Type {{ getSortIcon('type') }}
            </th>
            <th @click="emit('sort', 'date')" class="w-[120px] border-r border-b border-[#232333] text-center font-normal cursor-pointer hover:bg-white/5">
              Date {{ getSortIcon('date') }}
            </th>
            <th @click="emit('sort', 'estimated_sp')" class="w-[110px] border-r border-b border-[#232333] text-center font-normal cursor-pointer hover:bg-white/5">
              <div class="flex items-center justify-center gap-1">
                Estimated SP {{ getSortIcon('estimated_sp') }} <Info class="w-3.5 h-3.5" />
              </div>
            </th>
            <th @click="emit('sort', 'actual_sp')" class="w-[110px] border-r border-b border-[#232333] text-center font-normal cursor-pointer hover:bg-white/5">
              <div class="flex items-center justify-center gap-1">
                Actual SP {{ getSortIcon('actual_sp') }} <Info class="w-3.5 h-3.5" />
              </div>
            </th>
            <th class="w-10 border-b border-[#232333] text-center font-normal">
              <PlusCircle class="w-4 h-4 mx-auto" />
            </th>
          </tr>
        </thead>

        <!-- BODY -->
        <tbody>
          <tr v-for="task in tasks" :key="task.id" class="group h-10 border-b border-[#232333] hover:bg-[#1b1b2a]">
            <!-- Color strip -->
            <td class="relative p-0 border-r border-[#232333]">
              <div class="absolute inset-y-0 left-0 w-[6px] bg-[#C084FC]"></div>
            </td>
            <!-- Checkbox -->
            <td class="text-center border-r border-[#232333]">
              <input type="checkbox" class="rounded border-gray-700 bg-gray-800">
            </td>
            <!-- Task Name -->
            <td class="px-4 border-r border-[#232333]">
              <div class="flex items-center justify-between group">
                <input 
                  :value="task.task" 
                  @change="e => updateField(task, 'task', e.target.value)"
                  class="bg-transparent border-none outline-none w-full text-gray-200"
                />
                <div class="flex gap-2">
                  <MessageSquare class="w-4 h-4 text-white cursor-pointer" />
                </div>
              </div>
            </td>
            <!-- Developer -->
            <td class="text-center border-r border-[#232333] px-2">
              <input 
                :value="task.developer" 
                @change="e => updateField(task, 'developer', e.target.value)"
                placeholder="Dev"
                class="bg-transparent border-none outline-none w-full text-center text-gray-300"
              />
            </td>
            <!-- Status -->
            <td class="p-0 border-r border-[#232333]">
              <select 
                :value="task.status" 
                @change="e => updateField(task, 'status', e.target.value)"
                :style="getStatusStyle(task.status)"
                class="status-pill"
              >
                <option v-for="opt in statusOptions" :key="opt.name" :value="opt.name" class="bg-[#181824] text-white">
                  {{ opt.name }}
                </option>
              </select>
            </td>
            <!-- Priority -->
            <td class="p-0 border-r border-[#232333]">
              <select 
                :value="task.priority" 
                @change="e => updateField(task, 'priority', e.target.value)"
                :style="getPriorityStyle(task.priority)"
                class="status-pill"
              >
                <option v-for="opt in priorityOptions" :key="opt.name" :value="opt.name" class="bg-[#181824] text-white">
                  {{ opt.name }}
                </option>
              </select>
            </td>
            <!-- Type -->
            <td class="p-0 border-r border-[#232333]">
              <select 
                :value="task.type" 
                @change="e => updateField(task, 'type', e.target.value)"
                :style="getTypeStyle(task.type)"
                class="status-pill"
              >
                <option v-for="opt in typeOptions" :key="opt.name" :value="opt.name" class="bg-[#181824] text-white">
                  {{ opt.name }}
                </option>
              </select>
            </td>
            <!-- Date -->
            <td class="text-center border-r border-[#232333] relative group/date">
              <span class="text-[12px] text-gray-400 group-hover/date:hidden">
                {{ formatDate(task.date) }}
              </span>
              <input 
                type="date" 
                :value="task.date" 
                @change="e => updateField(task, 'date', e.target.value)"
                class="hidden group-hover/date:block bg-[#1B1B2A] border border-gray-700 rounded outline-none w-full text-center text-[12px] absolute inset-0 z-10"
              />
            </td>
            <!-- Estimated SP -->
            <td class="text-center border-r border-[#232333] text-gray-300">
              {{ task.estimated_sp || 0 }} SP
            </td>
            <!-- Actual SP -->
            <td class="text-center border-r border-[#232333] text-gray-300">
              {{ task.actual_sp || 0 }} SP
            </td>
            <td class="border-[#232333]"></td>
          </tr>

          <!-- ADD TASK ROW -->
          <tr class="h-10 hover:bg-[#1b1b2a] cursor-pointer text-gray-500 border-b border-[#232333]">
            <td class="border-r border-[#232333]"></td>
            <td class="text-center border-r border-[#232333]">
              <input type="checkbox" disabled class="rounded border-gray-800 bg-gray-800 opacity-50">
            </td>
            <td colspan="9" class="px-4 text-[13px]">
              <div class="flex items-center gap-2">
                <PlusCircle class="w-4 h-4" />
                Add task
              </div>
            </td>
          </tr>

          <!-- SUMMARY ROW -->
          <tr class="h-[60px] bg-[#141420] align-top">
            <td class="border-r border-[#232333]"></td>
            <td class="border-r border-[#232333]"></td>
            <td class="border-r border-[#232333]"></td>
            <td class="border-r border-[#232333]"></td>
            <!-- Status Stats -->
            <td class="border-r border-[#232333] p-3">
              <div class="flex h-5 rounded overflow-hidden">
                <div v-for="stat in statusStats" :key="stat.name" 
                     :style="{ width: stat.percent + '%', backgroundColor: stat.color }"
                     class="h-full"></div>
              </div>
            </td>
            <!-- Priority Stats -->
            <td class="border-r border-[#232333] p-3">
              <div class="flex h-5 rounded overflow-hidden">
                <div v-for="stat in priorityStats" :key="stat.name" 
                     :style="{ width: stat.percent + '%', backgroundColor: stat.color }"
                     class="h-full"></div>
              </div>
            </td>
            <!-- Type Stats -->
            <td class="border-r border-[#232333] p-3">
              <div class="flex h-5 rounded overflow-hidden">
                <div v-for="stat in typeStats" :key="stat.name" 
                     :style="{ width: stat.percent + '%', backgroundColor: stat.color }"
                     class="h-full"></div>
              </div>
            </td>
            <!-- Date empty -->
            <td class="border-r border-[#232333] p-3">
              <div class="h-5 bg-[#2D2D44] border border-[#3A3F50] rounded-full"></div>
            </td>
            <!-- Est SP Sum -->
            <td class="border-r border-[#232333] p-2 text-center">
              <div class="text-[13px] text-gray-300">{{ totalEstimated }} SP</div>
              <div class="text-[10px] text-gray-500 uppercase">sum</div>
            </td>
            <!-- Act SP Sum -->
            <td class="border-r border-[#232333] p-2 text-center">
              <div class="text-[13px] text-gray-300">{{ totalActual }} SP</div>
              <div class="text-[10px] text-gray-500 uppercase">sum</div>
            </td>
            <td></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
.avatar-placeholder {
  width: 28px;
  height: 28px;
  border-radius: 9999px;
  background-color: #2D2D44;
  border: 1px solid #3A3F50;
  display: flex;
  align-items: center;
  justify-content: center;
}

.status-pill {
  width: 100%;
  height: 40px;
  text-align: center;
  font-size: 11px;
  font-weight: 500;
  border: none;
  outline: none;
  appearance: none;
  cursor: pointer;
}

select {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}
</style>

