<div class="dropdown">
    <button class="btn btn-sm dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        {{ strtoupper(app()->getLocale()) }}
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
        <li><a class="dropdown-item {{ app()->getLocale() == 'ro' ? 'active' : '' }}" href="{{ route('shop.change.language', 'ro') }}">Română</a></li>
        <li><a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('shop.change.language', 'en') }}">English</a></li>
    </ul>
</div> 