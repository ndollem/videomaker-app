<div class="min-h-screen bg-gray-100 py-6 flex flex-col justify-center sm:py-12">
    <div class="relative py-3 sm:max-w-xl sm:mx-auto w-full">
        <div class="relative px-1 py-10 bg-white shadow-lg sm:rounded-3xl sm:px-6">
            <div class="max-w-md mx-auto">
                <div class="flex justify-center">
                    <h1 class="text-gray-700 font-bold text-3xl">VIDEOMAKER APP</h1>
                </div>
                <div class="divide-y divide-gray-200">
                    <div class="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">

                            <div>
                                <form method="post" action="{{ route('form.create') }}">
                                    @csrf
                                    <div>
                                        <label for="first-name" class="block text-sm font-medium text-gray-700">Choose Template :</label>
                                        <select name="template_id" wire:change="getTemplate" wire:model="template_id"
                                            class="mt-2 mb-3 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="5589dc1d-4e16-11e8-acd9-0ab5563f8f76">Template 1</option>
                                            <option value="488b47ec-1c47-11ec-a69f-0699ff9171cb">Template 2</option>
                                            <option value="399c7e94-b546-11ea-8ba0-06ef0ab4386c">Template 3</option>
                                        </select>

                                        @if ($template_variables)
                                            <label class="block text-sm font-medium text-gray-700">External ID :</label>
                                            <input type="text" name="external_id" value="video-{{ $template_id }}" class="mt-2 mb-3 py-2 px-3 border focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">

                                            <label class="block text-sm font-medium text-gray-700">Title :</label>
                                            <input type="text" name="title" value="{{ $template_title }}" class="mt-2 mb-3 py-2 px-3 border focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            @forelse ($template_variables as $v)

                                                @if ($v['type'] == 'text')
                                                    <label class="block text-sm font-medium text-gray-700">{{$v['name']}} :</label>
                                                    <textarea
                                                        name="variable_values[{{ $v['id'] }}]" class="resize-x mt-2 mb-3 py-2 px-3 border focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ $v['default'] }}</textarea>
                                                @endif

                                                @if ($v['type'] == 'image')
                                                    <label class="block text-sm font-medium text-gray-700">{{$v['name']}} :</label>
                                                    <input type="text" name="variable_values[{{ $v['id'] }}]"
                                                        value="{{ $v['default'] }}" class="mt-2 mb-3 py-2 px-3 border focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                @endif

                                                @if ($v['type'] == 'video')
                                                    <label class="block text-sm font-medium text-gray-700">{{$v['name']}} :</label>
                                                    <input type="text" name="variable_values[{{ $v['id'] }}]"
                                                        value="{{ $v['default'] }}" class="mt-2 mb-3 py-2 px-3 border focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                @endif

                                            @empty

                                            @endforelse

                                            <label class="block text-sm font-medium text-gray-700">Notifications (optional) :</label>
                                            <label class="inline-flex items-center text-sm font-medium text-gray-700 mr-2">
                                                <input type="radio" id="without-notification" wire:model="notification"
                                                name="notification" value="" class="form-radio">
                                                <span class="ml-2">Don't Send</span>
                                            </label>
                                            <label class="inline-flex items-center text-sm font-medium text-gray-700 mr-2">
                                                <input type="radio" id="youtube" name="notification" value="youtube"
                                                wire:model="notification" class="form-radion">
                                                <span class="ml-2">Youtube</span>
                                            </label>
                                            <label class="inline-flex items-center text-sm font-medium text-gray-700 mr-2">
                                                <input type="radio" id="gdrive" name="notification" value="gdrive"
                                                wire:model="notification">
                                                <span class="ml-2">Google Drive</span>
                                            </label>

                                            @if ($notification)
                                                <label class="block text-sm font-medium text-gray-700">{{ $notification }} ID :</label>
                                                <input type="text" name="notification_id" class="mt-1 py-2 px-3 border focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                <small>** Make sure that you connect your account to YouTube / Gdrive
                                                    via Moovly Dashboard first!</small>
                                            @endif

                                            <div class="flex w-full mt-4 justify-end">
                                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Create</button>
                                            </div>
                                        @endif
                                    </div>
                                </form>
                            </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- @if ($jobs) --}}
            <div class="flex w-full mt-4 justify-end">
                <button type="button" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" wire:click="getVideosData">Download Videos</button>
            </div>
        {{-- @endif --}}

        @if ($message)
            <div class="mt-8 relative px-1 py-10 bg-white shadow-lg sm:rounded-3xl sm:px-6">
                <div class="bg-white px-1 py-1 sm:rounded-3xl sm:px-6">
                    <div class="bg-gray-200 text-xs">
                        @php
                            print("<pre>".print_r($message,true)."</pre>");
                            print("<pre>".print_r($videoLists,true)."</pre>");
                        @endphp
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

