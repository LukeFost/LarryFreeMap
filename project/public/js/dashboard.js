document.addEventListener('DOMContentLoaded', function() {
    loadRecentViews();
    loadSavedSearches();
});

async function loadRecentViews() {
    // This will be implemented to load from backend
    const mockProperties = [
        {
            name: 'Sample Property 1',
            address: '123 Main St',
            price: '$1,200/month',
            image: 'https://via.placeholder.com/300x200'
        },
        // Add more mock data as needed
    ];

    const grid = document.querySelector('.property-grid');
    if (!grid) return;

    mockProperties.forEach(property => {
        const card = createPropertyCard(property);
        grid.appendChild(card);
    });
}

async function loadSavedSearches() {
    // This will be implemented to load from backend
    const mockSearches = [
        {
            criteria: '2 bed, 2 bath, < $1500',
            date: '2024-01-15'
        },
        // Add more mock data as needed
    ];

    const list = document.querySelector('.search-list');
    if (!list) return;

    mockSearches.forEach(search => {
        const item = createSearchItem(search);
        list.appendChild(item);
    });
}

function createPropertyCard(property) {
    const card = document.createElement('div');
    card.className = 'property-card';
    card.innerHTML = `
        <img src="${property.image}" alt="${property.name}">
        <div class="property-info">
            <h3>${property.name}</h3>
            <p>${property.address}</p>
            <p>${property.price}</p>
        </div>
    `;
    return card;
}

function createSearchItem(search) {
    const item = document.createElement('div');
    item.className = 'search-item';
    item.innerHTML = `
        <div>
            <strong>${search.criteria}</strong>
            <p>Saved on ${search.date}</p>
        </div>
        <button onclick="runSavedSearch(this)">Run Search</button>
    `;
    return item;
}

function runSavedSearch(button) {
    // This will be implemented to run the saved search
    const criteria = button.parentElement.querySelector('strong').textContent;
    console.log('Running saved search:', criteria);
}
