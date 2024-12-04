document.addEventListener('DOMContentLoaded', function() {
    loadFavorites();
});

async function loadFavorites() {
    // This will be implemented to load from backend
    const mockFavorites = [
        {
            name: 'Favorite Property 1',
            address: '123 Main St',
            price: '$1,200/month',
            image: 'https://via.placeholder.com/300x200'
        },
        {
            name: 'Favorite Property 2',
            address: '456 Oak Ave',
            price: '$1,500/month',
            image: 'https://via.placeholder.com/300x200'
        }
        // Add more mock data as needed
    ];

    const grid = document.querySelector('.property-grid');
    
    mockFavorites.forEach(property => {
        const card = createPropertyCard(property);
        grid.appendChild(card);
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
            <button onclick="removeFavorite(this)" class="remove-favorite">
                <i class="fas fa-heart-broken"></i> Remove from Favorites
            </button>
        </div>
    `;
    return card;
}

function removeFavorite(button) {
    // This will be implemented to remove from backend
    const card = button.closest('.property-card');
    card.remove();
}
