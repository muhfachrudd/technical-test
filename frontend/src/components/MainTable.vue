<script setup>
import { computed } from 'vue';
import { MessageSquare, Info, PlusCircle, User } from 'lucide-vue-next';

const props = defineProps(['tasks']);
const emit = defineEmits(['update', 'sort']);

const statusOptions = [
  { name: 'Waiting for review', color: '#97E4F7', textColor: '#0F0F17' },
  { name: 'In Progress', color: '#F6A64C', textColor: '#FFFFFF' },
  { name: 'Ready to start', color: '#2C64F5', textColor: '#FFFFFF' },
  { name: 'Pending Deploy', color: '#8B5CF6', textColor: '#FFFFFF' },
  { name: 'Done', color: '#1BC78D', textColor: '#FFFFFF' },
  { name: 'Stuck', color: '#EF4444', textColor: '#FFFFFF' }
];

const priorityOptions = [
  { name: 'Critical', color: '#EF4444' },
  { name: 'High', color: '#8B5CF6' },
  { name: 'Medium', color: '#3A86FF' },
  { name: 'Low', color: '#6B7280' },
  { name: 'Best Effort', color: '#94A3B8' }
];

const typeOptions = [
  { name: 'Feature Enhancements', color: '#F8BFC8', textColor: '#0F0F17' },
  { name: 'Other', color: '#C0B6F2', textColor: '#0F0F17' },
  { name: 'Bug', color: '#F43F5E', textColor: '#FFFFFF' }
];

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
  props.tasks.forEach(t => { counts[t[field]] = (counts[t[field]] || 0) + 1; });
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
</script>

<template>
  <div class="overflow-x-auto rounded-lg border border-[#232333]">
    <table class="main-table">
      <thead>
        <tr class="bg-transparent">
          <th class="w-10 text-center"><input type="checkbox" class="rounded border-gray-700 bg-gray-800"></th>
          <th class="w-2!"></th>
          <th class="min-w-[250px]" @click="$emit('sort', 'task')">Task</th>
          <th class="w-10 text-center"></th>
          <th class="w-24 text-center" @click="$emit('sort', 'developer')">Developer</th>
          <th class="w-32 text-center" @click="$emit('sort', 'status')">Status</th>
          <th class="w-32 text-center" @click="$emit('sort', 'priority')">Priority</th>
          <th class="w-32 text-center" @click="$emit('sort', 'type')">Type</th>
          <th class="w-28 text-center" @click="$emit('sort', 'date')">Date</th>
          <th class="w-24 text-center" @click="$emit('sort', 'estimated_sp')">Estimated SP <Info class="w-3 h-3 inline ml-1 opacity-50" /></th>
          <th class="w-24 text-center" @click="$emit('sort', 'actual_sp')">Actual SP <Info class="w-3 h-3 inline ml-1 opacity-50" /></th>
          <th class="w-10 text-center">+</th>
        </tr>
      </thead>
      <tbody class="bg-[#181824]">
        <tr v-for="task in tasks" :key="task.id" class="group hover:bg-white/5 transition-colors">
          <td class="relative w-10 text-center">
            <!-- Left border indicator -->
            <div class="absolute left-0 top-0 bottom-0 w-[3px] bg-purple-600"></div>
            <input type="checkbox" class="rounded border-gray-700 bg-gray-800">
          </td>
          <td class="w-2!"></td>
          <td class="px-3">
            <div class="flex items-center justify-between">
              <input :value="task.task" @change="e => updateField(task, 'task', e.target.value)" 
                     class="w-full py-2 bg-transparent outline-none">
              <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <MessageSquare class="w-3.5 h-3.5 text-gray-500 cursor-pointer" />
                <PlusCircle class="w-3.5 h-3.5 text-gray-500 cursor-pointer" />
              </div>
            </div>
          </td>
          <td class="text-center"></td>
          <td class="text-center">
            <div class="flex justify-center">
              <div class="w-7 h-7 rounded-full bg-[#2D2D44] border border-gray-700 flex items-center justify-center">
                 <User class="w-4 h-4 text-gray-400" />
              </div>
            </div>
          </td>
          <td class="p-0!">
            <select :value="task.status" @change="e => updateField(task, 'status', e.target.value)" 
                    class="w-full h-10 text-center text-[11px] font-semibold appearance-none outline-none" :style="getStatusStyle(task.status)">
              <option v-for="opt in statusOptions" :key="opt.name" :value="opt.name" class="bg-[#181824] text-white">{{ opt.name }}</option>
            </select>
          </td>
          <td class="p-0!">
            <select :value="task.priority" @change="e => updateField(task, 'priority', e.target.value)" 
                    class="w-full h-10 text-center text-[11px] font-semibold appearance-none outline-none" :style="getPriorityStyle(task.priority)">
              <option v-for="opt in priorityOptions" :key="opt.name" :value="opt.name" class="bg-[#181824] text-white">{{ opt.name }}</option>
            </select>
          </td>
          <td class="p-0!">
            <select :value="task.type" @change="e => updateField(task, 'type', e.target.value)" 
                    class="w-full h-10 text-center text-[11px] font-semibold appearance-none outline-none" :style="getTypeStyle(task.type)">
              <option v-for="opt in typeOptions" :key="opt.name" :value="opt.name" class="bg-[#181824] text-white">{{ opt.name }}</option>
            </select>
          </td>
          <td class="text-center">
            <input type="date" :value="task.date" @change="e => updateField(task, 'date', e.target.value)" class="text-center w-full text-[10px] opacity-60">
          </td>
          <td class="text-center">
            <div class="flex flex-col items-center">
                <span class="font-medium text-[12px]">{{ task.estimated_sp }} SP</span>
            </div>
          </td>
          <td class="text-center">
            <div class="flex flex-col items-center">
                <span class="font-medium text-[12px]">{{ task.actual_sp }} SP</span>
            </div>
          </td>
          <td class="text-center hover:bg-white/10 cursor-pointer"></td>
        </tr>

        <!-- Add Task italic row -->
        <tr class="hover:bg-white/5 cursor-pointer">
          <td class="relative">
             <div class="absolute left-0 top-0 bottom-0 w-[3px] bg-purple-600"></div>
          </td>
          <td colspan="11" class="px-5 py-3 text-[11px] italic text-[#5E5E7A] font-medium">+ Add task</td>
        </tr>

        <!-- Summary Statistics Row -->
        <tr class="bg-[#12121B] h-16">
          <td colspan="5" class="border-t border-[#232333]"></td>
          <td class="align-bottom p-0! border-t border-[#232333]">
             <div class="flex h-3 w-[85%] mx-auto rounded-full overflow-hidden mb-2">
                <div v-for="s in statusStats" :key="s.name" :style="{ width: s.percent + '%', backgroundColor: s.color }" class="h-full"></div>
             </div>
          </td>
          <td class="align-bottom p-0! border-t border-[#232333]">
             <div class="flex h-3 w-[85%] mx-auto rounded-full overflow-hidden mb-2">
                <div v-for="s in priorityStats" :key="s.name" :style="{ width: s.percent + '%', backgroundColor: s.color }" class="h-full"></div>
             </div>
          </td>
          <td class="align-bottom p-0! border-t border-[#232333]">
             <div class="flex h-3 w-[85%] mx-auto rounded-full overflow-hidden mb-2">
                <div v-for="s in typeStats" :key="s.name" :style="{ width: s.percent + '%', backgroundColor: s.color }" class="h-full"></div>
             </div>
          </td>
          <td class="border-t border-[#232333]">
             <div class="flex justify-center">
                <div class="bg-gray-800/80 px-4 py-1.5 rounded-full text-[10px] text-gray-500">-</div>
             </div>
          </td>
          <td class="text-center border-t border-[#232333]">
             <div class="font-bold text-gray-300 text-[12px]">{{ totalEstimated }} SP</div>
             <div class="text-[9px] uppercase text-gray-600 font-bold tracking-tighter">sum</div>
          </td>
          <td class="text-center border-t border-[#232333]">
             <div class="font-bold text-gray-300 text-[12px]">{{ totalActual }} SP</div>
             <div class="text-[9px] uppercase text-gray-600 font-bold tracking-tighter">sum</div>
          </td>
          <td class="border-t border-[#232333]"></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
/* Scoped styles kept minimal to avoid Tailwind v4 reference issues */
select {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}
</style>
