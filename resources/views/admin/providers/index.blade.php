<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Proveedores
            </h2>
            <a href="{{ route('admin.providers.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                Nuevo Proveedor
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('status'))
                        <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded relative"
                            role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Nombre
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Contacto
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Insumos
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($providers as $provider)
                                    <tr>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $provider->name }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $provider->contact_name ?? '—' }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $provider->supplies_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-4">
                                            <a href="{{ route('admin.providers.show', $provider) }}"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                                                Ver
                                            </a>
                                            <a href="{{ route('admin.providers.edit', $provider) }}"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                                                Editar
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"
                                            class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                            No hay proveedores registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $providers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold">Insumos disponibles</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Busca rápidamente un insumo y conoce a
                                qué proveedor pertenece.</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="supply-search"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buscar insumo</label>
                        <div class="mt-1 relative">
                            <input id="supply-search" type="text" placeholder="Escribe el nombre del insumo"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700"
                                autocomplete="off" data-search-url="{{ route('admin.supplies.search') }}">
                        </div>
                    </div>

                    <p id="supply-status" class="mt-4 text-sm text-gray-500 dark:text-gray-400">Escribe para buscar un
                        insumo.</p>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Insumo
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Proveedor
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Unidad
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Precio Unitario Sin IVA
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Precio Unitario Con IVA
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Stock
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="supply-results"
                                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            </tbody>
                        </table>
                    </div>
                    <div id="supply-pagination" class="mt-4 flex justify-center"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const searchInput = document.getElementById('supply-search');
                const resultsBody = document.getElementById('supply-results');
                const statusText = document.getElementById('supply-status');
                const paginationContainer = document.getElementById('supply-pagination');
                const searchUrl = searchInput?.dataset?.searchUrl;
                let debounceTimer;
                let activeRequest;
                let currentQuery = '';

                // Validaciones mínimas
                if (!searchUrl) {
                    console.error('Falta data-search-url en #supply-search');
                    if (statusText) {
                        statusText.classList.add('text-red-500');
                        statusText.textContent = 'No se configuró la URL de búsqueda.';
                    }
                    return;
                }

                // Utilidades
                const fmtCLP = new Intl.NumberFormat('es-CL', {
                    style: 'currency',
                    currency: 'CLP'
                });
                const toNumber = (v) => {
                    if (v === null || v === undefined) return NaN;
                    const n = typeof v === 'string' ? Number(v) : v;
                    return Number.isFinite(n) ? n : NaN;
                };

                const setStatus = (msg, isError = false) => {
                    statusText.classList.toggle('text-red-500', isError);
                    statusText.classList.toggle('text-gray-500', !isError);
                    statusText.classList.toggle('dark:text-gray-400', !isError);
                    statusText.textContent = msg;
                };

                const renderRows = (supplies) => {
                    resultsBody.innerHTML = '';

                    if (!Array.isArray(supplies) || supplies.length === 0) {
                        const row = document.createElement('tr');
                        const cell = document.createElement('td');
                        cell.colSpan = 6; // 6 columnas: nombre, proveedor, unidad, precio neto, precio c/IVA, stock
                        cell.className = 'px-4 py-4 text-sm text-center text-gray-500 dark:text-gray-400';
                        cell.textContent = 'No se encontraron insumos para la búsqueda realizada.';
                        row.appendChild(cell);
                        resultsBody.appendChild(row);
                        return;
                    }

                    supplies.forEach((supply) => {
                        const row = document.createElement('tr');

                        const nameCell = document.createElement('td');
                        nameCell.className =
                            'px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white';
                        nameCell.textContent = supply?.name ?? '—';

                        const providerCell = document.createElement('td');
                        providerCell.className =
                            'px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300';
                        providerCell.textContent = supply?.provider?.name ?? '—';

                        const unitCell = document.createElement('td');
                        unitCell.className =
                            'px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300';
                        unitCell.textContent = supply?.unit ?? '—';

                        // Precio neto (sin IVA)
                        const priceCell = document.createElement('td');
                        priceCell.className =
                            'px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300';
                        const net = toNumber(supply?.unit_price);
                        priceCell.textContent = Number.isFinite(net) ? fmtCLP.format(net) : '—';

                        // Precio con IVA (19%) = neto * 1.19
                        const priceCellCI = document.createElement('td');
                        priceCellCI.className =
                            'px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300';
                        const gross = Number.isFinite(net) ? net * 1.19 : NaN;
                        priceCellCI.textContent = Number.isFinite(gross) ? fmtCLP.format(gross) : '—';

                        const stockCell = document.createElement('td');
                        stockCell.className =
                            'px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300';
                        stockCell.textContent = supply?.stock ?? '—';

                        row.appendChild(nameCell);
                        row.appendChild(providerCell);
                        row.appendChild(unitCell);
                        row.appendChild(priceCell);
                        row.appendChild(priceCellCI);
                        row.appendChild(stockCell);

                        resultsBody.appendChild(row);
                    });
                };

                const renderPagination = (payload) => {
                    if (!paginationContainer) {
                        return;
                    }

                    paginationContainer.innerHTML = '';

                    const links = Array.isArray(payload?.links) ? payload.links : [];

                    if (links.length <= 3) {
                        paginationContainer.classList.add('hidden');
                        return;
                    }

                    paginationContainer.classList.remove('hidden');

                    const nav = document.createElement('nav');
                    nav.className = 'inline-flex -space-x-px rounded-md shadow-sm';
                    nav.setAttribute('aria-label', 'Paginación de insumos');

                    links.forEach((link) => {
                        const isDisabled = !link.url;
                        const isActive = Boolean(link.active);
                        const element = document.createElement(isDisabled ? 'span' : 'button');
                        element.className = [
                            'relative inline-flex items-center px-3 py-2 border text-sm font-medium focus:outline-none focus:z-20',
                            isActive
                                ? 'z-10 border-indigo-500 bg-indigo-50 text-indigo-600 dark:border-indigo-400 dark:bg-indigo-900/40 dark:text-indigo-100'
                                : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800',
                            isDisabled && !isActive
                                ? 'cursor-default text-gray-400 dark:text-gray-500 hover:bg-white dark:hover:bg-gray-900'
                                : ''
                        ].join(' ');

                        const tmp = document.createElement('span');
                        tmp.innerHTML = link.label ?? '';
                        let label = tmp.textContent || tmp.innerText || '';
                        const normalized = label.trim().toLowerCase();
                        if (normalized.includes('previous')) {
                            label = 'Anterior';
                        } else if (normalized.includes('next')) {
                            label = 'Siguiente';
                        }
                        element.textContent = label;

                        if (!isDisabled) {
                            element.type = 'button';
                            element.addEventListener('click', () => {
                                try {
                                    const url = new URL(link.url, window.location.origin);
                                    const nextPage = Number(url.searchParams.get('page')) || 1;
                                    fetchSupplies(currentQuery, nextPage);
                                } catch (error) {
                                    console.error('No se pudo navegar a la página solicitada.', error);
                                }
                            });
                        }

                        nav.appendChild(element);
                    });

                    paginationContainer.appendChild(nav);
                };

                const updateStatus = (meta, query) => {
                    if (!meta) {
                        setStatus('Mostrando los primeros insumos registrados.');
                        return;
                    }

                    if ((meta.total ?? 0) === 0) {
                        setStatus('No se encontraron insumos para la búsqueda realizada.');
                        return;
                    }

                    const from = meta.from ?? 1;
                    const to = meta.to ?? meta.per_page ?? 5;
                    const total = meta.total ?? to;
                    const baseMessage = query
                        ? `Resultados para "${query}".`
                        : 'Mostrando los insumos registrados.';

                    setStatus(`${baseMessage} Mostrando ${from}-${to} de ${total} insumos.`);
                };

                const fetchSupplies = (query = '', page = 1) => {
                    // Cancela la request anterior si sigue activa
                    if (activeRequest) activeRequest.abort();
                    activeRequest = new AbortController();

                    const params = new URLSearchParams();
                    if (query) params.set('q', query);
                    if (Number.isFinite(page) && page > 1) params.set('page', page);

                    setStatus('Buscando insumos...');

                    const url = params.toString() ? `${searchUrl}?${params.toString()}` : searchUrl;

                    fetch(url, {
                            signal: activeRequest.signal
                        })
                        .then((response) => {
                            if (!response.ok) throw new Error(
                                'No se pudo obtener la información de los insumos.');
                            return response.json();
                        })
                        .then((data) => {
                            currentQuery = query;
                            const items = Array.isArray(data?.data) ? data.data : (Array.isArray(data) ? data :
                                []);
                            renderRows(items);
                            renderPagination(data);
                            updateStatus(data?.meta, query);
                        })
                        .catch((error) => {
                            if (error.name === 'AbortError') return; // request cancelada
                            if (paginationContainer) {
                                paginationContainer.innerHTML = '';
                                paginationContainer.classList.add('hidden');
                            }
                            console.error(error);
                            setStatus('Ocurrió un error al cargar los insumos.', true);
                        });
                };

                const handleInput = () => {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        fetchSupplies(searchInput.value.trim(), 1);
                    }, 300);
                };

                // Accesibilidad recomendada
                statusText.setAttribute('role', 'status');
                statusText.setAttribute('aria-live', 'polite');

                searchInput.addEventListener('input', handleInput);

                // Carga inicial
                fetchSupplies();
            });
        </script>
    @endpush
</x-app-layout>
