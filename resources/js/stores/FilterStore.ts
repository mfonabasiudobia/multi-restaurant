import { defineStore } from 'pinia'
import { ref, reactive } from 'vue'
import { useMaster } from './MasterStore'

interface Category {
    name: string;
    [key: string]: any;
}

interface FilterOption {
    name: string;
    [key: string]: any;
}

interface FilterState {
    [key: string]: { [key: string]: boolean }
}

export const useFilterStore = defineStore('filter', () => {
    const activeFilter = ref<string | null>(null)
    const popupPosition = reactive({ top: '0px', left: '0px', minWidth: '200px', transform: 'none' })
    
    // Initialize filters as reactive object
    const filters = reactive<FilterState>({
        Categories: {},
        Size: {},
        Color: {},
        Season: {},
        Quality: {}
    })

    // Save filters to localStorage
    const saveFilters = () => {
        localStorage.setItem('filters', JSON.stringify(filters))
    }

    // Set filters with new values
    const setFilters = (newFilters: any) => {
        Object.keys(filters).forEach(key => {
            filters[key] = { ...newFilters[key] } || {}
        })
        saveFilters()
    }

    const masterStore = useMaster()

    // Get available options from master store
    const getFilterOptions = (filterType: string) => {
        switch (filterType) {
            case 'Categories':
                return [];
            case 'Size':
                return masterStore.sizes?.map((s: FilterOption) => s.name) || []
            case 'Color':
                return masterStore.colors?.map((c: FilterOption) => c.name) || []
            case 'Season':
                return masterStore.seasons?.map((s: FilterOption) => s.name) || []
            case 'Quality':
                return masterStore.qualities?.map((q: FilterOption) => q.name) || []
            default:
                return []
        }
    }

    const setPopupPosition = () => {
        // Set fixed position for the popup
        popupPosition.top = '50%'; // Center vertically
        popupPosition.left = '50%'; // Center horizontally
        popupPosition.minWidth = '300px'; // Set a minimum width
        // Optionally, you can add a transform to center it
        popupPosition.transform = 'translate(-50%, -50%)'; // Center the popup
    }

    const toggleFilter = (filterName: string, element: HTMLElement) => {
        if (activeFilter.value === filterName) {
            activeFilter.value = null
            return
        }
        
        setPopupPosition()
        activeFilter.value = filterName
    }

    const clearFilters = () => {
        Object.keys(filters).forEach((key) => {
            filters[key] = {}
        })
        activeFilter.value = null
        saveFilters()
    }

    // Get active filters
    const getActiveFilters = () => {
        const active: Record<string, string[]> = {}
        
        Object.entries(filters).forEach(([key, value]) => {
            const selected = Object.entries(value)
                .filter(([_, isSelected]) => isSelected)
                .map(([option]) => option)
            
            if (selected.length > 0) {
                active[key] = selected
            }
        })
        
        return active
    }

    return {
        activeFilter,
        popupPosition,
        filters,
        toggleFilter,
        setPopupPosition,
        clearFilters,
        getFilterOptions,
        getActiveFilters,
        setFilters,
        saveFilters
    }
}, {
    persist: true
})