@if (isset($name))

    <div class="modal fade" id="quickEditModal" tabindex="-1" role="dialog" aria-labelledby="quickEditModalLabel"
        aria-hidden="true" style="margin-top: 100px;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quickEditModalLabel">Modifica Rapida</h5>
                    <button type="button" class="btn close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="quickEditForm" method="POST" action="">

                        @csrf
                        <input type="hidden" id="post_id" name="post_id" value="{{ $postId }}">

                        <!-- Name -->
                        <div class="form-group">
                            <label for="post_name">Titolo</label>
                            <input type="text" id="post_name" name="name" class="form-control"
                                value="{{ $name }}">
                        </div>

                        <!-- Slug -->
                        <div class="form-group">
                            <label for="post_slug">Slug</label>
                            <input type="text" id="post_slug" name="slug" class="form-control"
                                value="{{ $slug }}">
                        </div>

                        <!-- Data (Scheduled Publishing) -->
                        <div class="form-group">
                            <label>Data</label>
                            <div class="row">
                                <!-- Day -->
                                <div class="col-4 col-sm-4 col-md-2 mb-2">
                                    <input type="number" name="day" id="post_day" class="form-control"
                                        placeholder="01" min="1" max="31" value="{{ $day }}">
                                </div>
                                <!-- Month -->
                                <div class="col-8 col-sm-4 col-md-2 mb-2">
                                    <select name="month" id="post_month" class="form-control">
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}"
                                                @if ((int) $month === $m) selected @endif>
                                                {{ \Carbon\Carbon::createFromFormat('!m', $m)->format('M') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <!-- Year -->
                                <div class="col-6 col-sm-4 col-md-2 mb-2">
                                    <input type="number" name="year" id="post_year" class="form-control"
                                        placeholder="2025" min="2023" value="{{ $year }}">
                                </div>
                                <!-- "alle" label -->
                                <div
                                    class="col-6 col-sm-2 col-md-1 mb-2 d-flex align-items-center justify-content-center">
                                    alle
                                </div>
                                <!-- Hour -->
                                <div class="col-6 col-sm-4 col-md-2 mb-2">
                                    <input type="number" name="hour" id="post_hour" class="form-control"
                                        min="0" max="23" placeholder="06" value="{{ $hour }}">
                                </div>
                                <!-- Minute -->
                                <div class="col-6 col-sm-4 col-md-2 mb-2">
                                    <input type="number" name="minute" id="post_minute" class="form-control"
                                        min="0" max="59" placeholder="30" value="{{ $minute }}">
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label for="post_status">Stato</label>
                            <select id="post_status" name="status" class="form-control">
                                <option value="published" @if ($status === 'published') selected @endif>Published
                                </option>
                                <option value="draft" @if ($status === 'draft') selected @endif>Draft</option>
                            </select>
                        </div>

                        <!-- Categories -->
                        <div class="form-group">
                            <label for="categories">Categorie</label>
                            <!-- Add a container with a set height and overflow -->
                            <div
                                style="max-height: 150px; overflow-y: auto; border: 1px solid #dddddd1f; padding: 5px;">
                                @if (isset($categories) && is_array($categories))
                                    @foreach ($categories as $catId => $catName)
                                        <label style="display: block;">
                                            <input type="checkbox" name="categories[]" value="{{ $catId }}"
                                                @if (in_array($catId, $selectedCategories)) checked @endif>
                                            {{ $catName }}
                                        </label>
                                    @endforeach
                                @else
                                    <!-- Fallback static list -->
                                    <label style="display: block;">
                                        <input type="checkbox" name="categories[]" value="1"> Archivio Sondaggi
                                    </label>
                                    <label style="display: block;">
                                        <input type="checkbox" name="categories[]" value="2"> Featured
                                    </label>
                                @endif
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="form-group">
                            <label for="post_tags">Tag</label>
                            <textarea id="post_tags" name="tags" class="form-control" placeholder="Separa i tag con delle virgole">{{ $tags }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Aggiorna</button>
                        <button class="btn btn-outline-secondary close">Annulla</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endif
