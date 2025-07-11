<div class="dropdown">
    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
        {{ strtoupper(app()->getLocale()) }}
    </button>
    <ul class="dropdown-menu">
        <li>
            @if(request()->is('admin*'))
                <form action="{{ route('admin.language.change') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="locale" value="ro">
                    <button type="submit" class="dropdown-item {{ app()->getLocale() == 'ro' ? 'active' : '' }}">
                        Română
                    </button>
                </form>
            @elseif(request()->is('shop*'))
                <a class="dropdown-item {{ app()->getLocale() == 'ro' ? 'active' : '' }}" 
                   href="{{ route('shop.language.change', 'ro') }}">
                    Română
                </a>
            @endif
        </li>
        <li>
            @if(request()->is('admin*'))
                <form action="{{ route('admin.language.change') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="locale" value="en">
                    <button type="submit" class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                        English
                    </button>
                </form>
            @elseif(request()->is('shop*'))
                <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" 
                   href="{{ route('shop.language.change', 'en') }}">
                    English
                </a>
            @endif
        </li>
    </ul>
</div> 