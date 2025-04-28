<x-filament-panels::page>
    <div class="mb-6">
        <x-filament::section>
            <div class="">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ $this->selectedProject ? $this->selectedProject->name : 'Select Project' }}
                    </h2>
                    <div>
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model.live="selectedProjectId"
                                wire:change="selectProject($event.target.value)">
                                <option value=0 selected>Select Project</option>
                                @foreach ($this->projects as $project)
                                    <option value="{{ $project->id }}">
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>
                </div>
                <p class="text-base font-extralight dark:text-white">
                    {{ $this->selectedProject ? $this->selectedProject->description : '' }}
                </p>
            </div>
        </x-filament::section>
    </div>

    @if ($this->selectedProject)
        <div class="overflow-x-auto pb-6" id="board-container">
            <div class="flex gap-x-4">
                @foreach ($this->taskStatuses as $status)
                    <div data-status-id="{{ $status->id }}"
                        class="status-column flex-1 min-w-[300px] rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col bg-gray-50 dark:bg-gray-900">
                        <div
                            class="px-4 py-3 rounded-t-xl bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-medium text-gray-900 dark:text-white flex items-center justify-between">
                                <span class="title">
                                    @switch($status->name)
                                        @case('todo')
                                            ToDo
                                        @break

                                        @case('in_progress')
                                            In Progress
                                        @break

                                        @case('done')
                                            Done
                                        @break

                                        @default
                                            {{ $status->name }}
                                    @endswitch
                                </span> <span
                                    class="text-gray-500 dark:text-gray-400 text-sm">{{ isset($this->tasks[$status->id]) ? $this->tasks[$status->id]->count() : 0 }}</span>
                            </h3>
                        </div>

                        <div class="container p-3 flex flex-col gap-3 h-[calc(100vh-20rem)] overflow-y-auto">
                            @foreach ($this->tasks[$status->id] ?? [] as $task)
                                <div
                                    class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                                    <div class="task-card  cursor-move h-fit" data-task-id="{{ $task->code }}"
                                        draggable="true">
                                        <div class="flex justify-between items-center mb-2">
                                            <span
                                                class="text-xs font-mono text-primary-500 border border-primary-500 dark:text-primary-400 px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 rounded">
                                                {{ $task->code }}
                                            </span>
                                        </div>
                                        <div class="space-y-4">
                                            <div class="">{{ $task->name }}</div>
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="flex items-center justify-center w-6 h-6 rounded-full bg-primary-500 text-white text-xs font-medium">
                                                    {{ strtoupper(substr($task->user->name, 0, 1)) }}
                                                </div>
                                                <span class="text-xs">{{ $task->user->name }}</span>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="mt-4 text-right text-sm cursor-pointer">
                                        <a wire:click="showDetailTask({{ $task->id }})"
                                            class="text-primary-500 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-600">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <script>
        let isDragging = false;
        let selectedParents = null;

        const updateStatus = async (statusId, taskId) => {
            try {
                await @this.call('updateTaskStatus', taskId, statusId);
            } catch (error) {
                console.error('Update failed:', error);
            }
        }

        document.addEventListener('dragstart', (e) => {
            if (e.target.classList.contains('task-card')) {
                isDragging = true;
                selectedParents = null;
                e.dataTransfer.setData('text/plain', e.target.id);

                document.querySelectorAll('.status-column').forEach(col => {
                    col.classList.add('transition-colors');
                });
            }
        });

        document.addEventListener('dragover', (e) => {
            if (!isDragging) return;

            const column = e.target.closest('.status-column');
            if (column) {
                e.preventDefault();
                document.querySelectorAll('.status-column').forEach(col => {
                    col.classList.remove('border-primary-500', 'dark:border-primary-500');
                });
                column.classList.add('border-primary-500', 'dark:border-primary-500');
                selectedParents = column;
            }
        });

        document.addEventListener('dragend', (e) => {
            if (!e.target.classList.contains('task-card')) return;

            document.querySelectorAll('.status-column').forEach(col => {
                col.classList.remove('border-primary-500', 'dark:border-primary-500', 'transition-colors');
            });

            if (selectedParents) {
                const status = selectedParents.getAttribute('data-status-id');
                const taskId = e.target.getAttribute('data-task-id');
                updateStatus(status, taskId);
            }

            isDragging = false;
            selectedParents = null;
        });

        document.addEventListener('livewire:init', () => {
            console.log('Livewire initialized');
        });

        document.addEventListener('livewire:update', () => {
            console.log('Livewire updated DOM');
        });
    </script>

</x-filament-panels::page>
