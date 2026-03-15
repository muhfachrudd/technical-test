import { STATUSES } from './constants';

export const INITIAL_TASKS = [
  { 
    id: 1, 
    task: 'Committed Feature', 
    developer: 'Jane', 
    status: STATUSES.READY_TO_START, 
    priority: 'High', 
    type: 'Other', 
    date: '2024-03-15', 
    estimated_sp: 2, 
    actual_sp: 1.5 
  },
  { 
    id: 2, 
    task: 'New Task', 
    developer: 'Doe', 
    status: STATUSES.IN_PROGRESS, 
    priority: 'Best Effort', 
    type: 'Feature Enhancement', 
    date: '2024-03-16', 
    estimated_sp: 0, 
    actual_sp: 0 
  },
  { 
    id: 3, 
    task: 'New task', 
    developer: 'Jane', 
    status: STATUSES.WAITING_FOR_REVIEW, 
    priority: 'Medium', 
    type: 'Feature Enhancement', 
    date: '2024-03-17', 
    estimated_sp: 0, 
    actual_sp: 0 
  },
  { 
    id: 4, 
    task: 'Done Task', 
    developer: 'John', 
    status: STATUSES.DONE, 
    priority: 'Low', 
    type: 'Other', 
    date: '2024-03-18', 
    estimated_sp: 0, 
    actual_sp: 0 
  }
];
