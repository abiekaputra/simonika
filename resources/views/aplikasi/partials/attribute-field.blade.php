<div class="mb-3" data-attribute-field="{{ $atribut->id_atribut }}">
    <label class="form-label" for="atribut_{{ $atribut->id_atribut }}">
        {{ $atribut->nama_atribut }}
        <small class="text-muted">({{ ucfirst($atribut->tipe_data) }})</small>
    </label>

    @if ($atribut->tipe_data === 'text')
        <textarea
            class="form-control"
            id="atribut_{{ $atribut->id_atribut }}"
            name="atribut[{{ $atribut->id_atribut }}]"
            rows="3"
        ></textarea>
    @elseif ($atribut->tipe_data === 'enum')
        <select
            class="form-select"
            id="atribut_{{ $atribut->id_atribut }}"
            name="atribut[{{ $atribut->id_atribut }}]"
        >
            <option value="">Pilih opsi</option>
            @foreach ($atribut->enum_options ?? [] as $option)
                <option value="{{ $option }}">{{ $option }}</option>
            @endforeach
        </select>
    @else
        <input
            class="form-control"
            id="atribut_{{ $atribut->id_atribut }}"
            name="atribut[{{ $atribut->id_atribut }}]"
            type="{{ $atribut->tipe_data === 'number' ? 'number' : ($atribut->tipe_data === 'date' ? 'date' : 'text') }}"
            @if ($atribut->tipe_data === 'number') step="any" @endif
        />
    @endif
</div>
