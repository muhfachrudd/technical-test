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
<div class="overflow-x-auto rounded-lg border border-[#232333] bg-[#141420]">
<table class="w-full table-fixed text-[12px] text-gray-300">

<!-- HEADER -->
<thead class="text-gray-400">
<tr class="h-10 border-b border-[#232333]">

<th class="w-10 text-center">
<input type="checkbox" class="rounded border-gray-700 bg-gray-800">
</th>

<th class="w-[260px] text-left px-3">Task</th>

<th class="w-[100px] text-center">Developer</th>

<th class="w-[160px] text-center">Status</th>

<th class="w-[140px] text-center">Priority</th>

<th class="w-[160px] text-center">Type</th>

<th class="w-[120px] text-center">Date</th>

<th class="w-[100px] text-center">Estimated SP</th>

<th class="w-[100px] text-center">Actual SP</th>

<th class="w-10 text-center">+</th>

</tr>
</thead>

<!-- BODY -->
<tbody class="bg-[#181824]">

<tr
v-for="task in tasks"
:key="task.id"
class="group h-10 border-b border-[#1f1f2f] hover:bg-[#1b1b2a]"
>

<!-- checkbox -->
<td class="relative text-center">

<div class="absolute left-0 top-0 bottom-0 w-[3px] bg-purple-600"></div>

<input type="checkbox" class="rounded border-gray-700 bg-gray-800">

</td>

<!-- task -->
<td class="px-3">

<div class="flex items-center justify-between">

<input
:value="task.task"
@change="e => updateField(task,'task',e.target.value)"
class="w-full bg-transparent outline-none"
/>

<div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">

<MessageSquare class="w-4 h-4 text-gray-500 cursor-pointer"/>

<PlusCircle class="w-4 h-4 text-gray-500 cursor-pointer"/>

</div>

</div>

</td>

<!-- developer -->
<td class="text-center">

<div class="flex justify-center">

<div class="w-7 h-7 rounded-full bg-[#2D2D44] border border-gray-700 flex items-center justify-center">

<User class="w-4 h-4 text-gray-400"/>

</div>

</div>

</td>

<!-- status -->
<td class="p-0">

<select
:value="task.status"
@change="e => updateField(task,'status',e.target.value)"
:style="getStatusStyle(task.status)"
class="w-full h-10 text-[11px] font-semibold text-center appearance-none outline-none"
>

<option
v-for="opt in statusOptions"
:key="opt.name"
:value="opt.name"
class="bg-[#181824]"
>

{{ opt.name }}

</option>

</select>

</td>

<!-- priority -->
<td class="p-0">

<select
:value="task.priority"
@change="e => updateField(task,'priority',e.target.value)"
:style="getPriorityStyle(task.priority)"
class="w-full h-10 text-[11px] font-semibold text-center appearance-none outline-none"
>

<option
v-for="opt in priorityOptions"
:key="opt.name"
:value="opt.name"
class="bg-[#181824]"
>

{{ opt.name }}

</option>

</select>

</td>

<!-- type -->
<td class="p-0">

<select
:value="task.type"
@change="e => updateField(task,'type',e.target.value)"
:style="getTypeStyle(task.type)"
class="w-full h-10 text-[11px] font-semibold text-center appearance-none outline-none"
>

<option
v-for="opt in typeOptions"
:key="opt.name"
:value="opt.name"
class="bg-[#181824]"
>

{{ opt.name }}

</option>

</select>

</td>

<!-- date -->
<td class="text-center">

<input
type="date"
:value="task.date"
@change="e => updateField(task,'date',e.target.value)"
class="w-full text-center text-[11px] bg-transparent outline-none"
/>

</td>

<!-- estimated -->
<td class="text-center font-medium">

{{ task.estimated_sp }} SP

</td>

<!-- actual -->
<td class="text-center font-medium">

{{ task.actual_sp }} SP

</td>

<td></td>

</tr>

<!-- ADD TASK -->
<tr class="h-10 hover:bg-[#1b1b2a] cursor-pointer">

<td></td>

<td colspan="9" class="italic text-gray-500 px-3">

+ Add task

</td>

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
