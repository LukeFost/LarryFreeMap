document.addEventListener('DOMContentLoaded', function() {
    // Tab Navigation
    const navLinks = document.querySelectorAll('.nav-links li');
    const tabContents = document.querySelectorAll('.tab-content');

    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            const tabId = link.getAttribute('data-tab');
            
            // Update active states
            navLinks.forEach(l => l.classList.remove('active'));
            tabContents.forEach(t => t.classList.remove('active'));
            
            link.classList.add('active');
            document.getElementById(tabId).classList.add('active');
            
            // Load data for the active tab
            loadTabData(tabId);
        });
    });

    // Initial load of overview data
    loadOverviewData();
});

async function loadOverviewData() {
    try {
        const response = await fetch('../api/admin/overview.php');
        const data = await response.json();
        
        document.getElementById('provider-count').textContent = data.providers;
        document.getElementById('building-count').textContent = data.buildings;
        document.getElementById('unit-count').textContent = data.units;
        document.getElementById('available-units').textContent = data.available_units;
    } catch (error) {
        console.error('Error loading overview data:', error);
    }
}

async function loadTabData(tabId) {
    switch(tabId) {
        case 'providers':
            await loadProviders();
            break;
        case 'buildings':
            await loadBuildings();
            break;
        case 'units':
            await loadUnits();
            break;
    }
}

async function loadProviders() {
    try {
        const response = await fetch('../api/admin/providers.php');
        const providers = await response.json();
        
        const tbody = document.querySelector('#providers-table tbody');
        tbody.innerHTML = '';
        
        providers.forEach(provider => {
            const row = `
                <tr>
                    <td>${provider.id}</td>
                    <td>${provider.name}</td>
                    <td>${provider.email}</td>
                    <td>${provider.phone}</td>
                    <td>${provider.building_count}</td>
                    <td>
                        <button onclick="editProvider(${provider.id})" class="action-btn edit-btn">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteProvider(${provider.id})" class="action-btn delete-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    } catch (error) {
        console.error('Error loading providers:', error);
    }
}

// Similar functions for buildings and units...

async function deleteItem(type, id) {
    const modal = document.getElementById('confirmModal');
    const confirmBtn = document.getElementById('confirmDelete');
    const cancelBtn = document.getElementById('cancelDelete');
    
    modal.style.display = 'block';
    
    return new Promise((resolve, reject) => {
        confirmBtn.onclick = async () => {
            try {
                const response = await fetch(`../api/admin/${type}.php`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id })
                });
                
                if (response.ok) {
                    modal.style.display = 'none';
                    resolve(true);
                } else {
                    throw new Error('Delete failed');
                }
            } catch (error) {
                console.error(`Error deleting ${type}:`, error);
                reject(error);
            }
        };
        
        cancelBtn.onclick = () => {
            modal.style.display = 'none';
            resolve(false);
        };
    });
}

// Helper functions for delete operations
async function deleteProvider(id) {
    if (await deleteItem('providers', id)) {
        loadProviders();
    }
}

async function deleteBuilding(id) {
    if (await deleteItem('buildings', id)) {
        loadBuildings();
    }
}

async function deleteUnit(id) {
    if (await deleteItem('units', id)) {
        loadUnits();
    }
}
