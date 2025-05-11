<?php
$path = $_SERVER['SCRIPT_NAME'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery - Magic Sole</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            color: #333;
            display: flex;
        }

        header {
            background-color: #1a1a1a;
            color: white;
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
            animation: slideInLeft 1s ease-out;
        }

        .logo img {
            width: 120px;
            margin-bottom: 2rem;
        }

        nav {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 100%;
        }

        nav a {
            color: #e3e3e3;
            text-decoration: none;
            font-size: 1.4rem;
            padding: 10px;
            transition: background 0.3s, color 0.3s;
            border-radius: 8px;
            text-align: center;
        }

        nav a:hover {
            background: #f9c303;
            color: #1a1a1a;
        }

        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 50px;
        }

        .hero {
            position: relative;
            background: linear-gradient(135deg, #d4af37, #f9c303);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            color: #1a1a1a;
            animation: fadeIn 1s ease-out;
            text-align: center;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('CoolGrey.gif') no-repeat center center/cover;
            opacity: 0.2;
            z-index: 0;
            transform: translateY(0);
            transition: transform 0.1s ease-out;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero-content p {
            font-size: 1.2rem;
            font-weight: 300;
        }

        .gallery-section {
            max-width: 1200px;
            margin: 40px auto;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: translateY(50px);
            animation: fadeInUp 1s forwards 0.5s;
        }

        .gallery-section h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #1a1a1a;
            text-align: center;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .filter-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .filter-buttons button {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #1a1a1a;
            border-radius: 10px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-buttons button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .filter-buttons button.active {
            background: linear-gradient(135deg, #d4af37, #f9c303);
            color: #1a1a1a;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .gallery-grid {
            column-count: 3;
            column-gap: 20px;
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            break-inside: avoid;
            opacity: 0;
            transform: scale(0.9);
            animation: galleryItemFadeIn 0.5s forwards;
        }

        .gallery-item:nth-child(1) { animation-delay: 0.6s; }
        .gallery-item:nth-child(2) { animation-delay: 0.7s; }
        .gallery-item:nth-child(3) { animation-delay: 0.8s; }
        .gallery-item:nth-child(4) { animation-delay: 0.9s; }
        .gallery-item:nth-child(5) { animation-delay: 1.0s; }
        .gallery-item:nth-child(6) { animation-delay: 1.1s; }

        .gallery-item img, .gallery-item video {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.3s ease;
        }

        .gallery-item:hover img, .gallery-item:hover video {
            transform: scale(1.05);
        }

        .gallery-item .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .gallery-item:hover .overlay {
            opacity: 1;
        }

        .gallery-item .overlay i {
            color: #f9c303;
            font-size: 2rem;
            transition: color 0.3s ease;
        }

        .gallery-item:hover .overlay i {
            color: #d4af37;
        }

        .gallery-item .overlay .caption {
            color: #fff;
            font-size: 1rem;
            margin-top: 10px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }

        .gallery-item:hover .overlay .caption {
            opacity: 1;
            transform: translateY(0);
        }

        .gallery-item video {
            background: #000;
        }

        .gallery-item.loading {
            background: #e0e0e0;
            height: 300px;
            animation: pulse 1.5s infinite;
        }

        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .lightbox-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
            animation: lightboxFadeIn 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .lightbox-content img, .lightbox-content video {
            width: 100%;
            height: auto;
            max-height: 70vh;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        }

        .lightbox-content .caption {
            color: #fff;
            font-size: 1.2rem;
            margin-top: 15px;
            opacity: 0;
            transform: translateY(20px);
            animation: captionFadeIn 0.5s forwards 0.3s;
        }

        .lightbox .close-btn, .lightbox .nav-btn {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
        }

        .lightbox .close-btn:hover, .lightbox .nav-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .lightbox .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 1.5rem;
            cursor: pointer;
            z-index: 1001;
        }

        .lightbox .nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            cursor: pointer;
        }

        .lightbox .prev-btn {
            left: 20px;
        }

        .lightbox .next-btn {
            right: 20px;
        }

        footer {
            font-size: 0.9rem;
            color: white;
            text-align: center;
            padding: 1rem 0;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 250px;
            background-color: #1a1a1a;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
        }

        .admin-controls {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .admin-controls button {
            padding: 10px 20px;
            background: #f9c303;
            color: #1a1a1a;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }

        .admin-controls button:hover {
            background: #d4af37;
        }

        .gallery-item .overlay .admin-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .gallery-item .overlay .admin-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background 0.3s;
        }

        .gallery-item .overlay .update-btn {
            background-color: #f9c303;
            color: #1a1a1a;
        }

        .gallery-item .overlay .update-btn:hover {
            background-color: #d4af37;
        }

        .gallery-item .overlay .delete-btn {
            background-color: #ff4444;
            color: #fff;
        }

        .gallery-item .overlay .delete-btn:hover {
            background-color: #cc0000;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
            position: relative;
            opacity: 0;
            transform: scale(0.8);
            animation: modalFadeIn 0.3s forwards;
        }

        .modal-content h3 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #1a1a1a;
        }

        .modal-content label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #1a1a1a;
        }

        .modal-content input, .modal-content select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            outline: none;
        }

        .modal-content input:focus, .modal-content select:focus {
            border: 1px solid #d4af37;
        }

        .modal-content button {
            padding: 10px 20px;
            background: #f9c303;
            color: #1a1a1a;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal-content button:hover {
            background: #d4af37;
        }

        .modal-content .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 1.5rem;
            color: #1a1a1a;
            cursor: pointer;
        }

        @keyframes modalFadeIn {
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 20px;
            }

            header {
                width: 100%;
                height: auto;
                position: relative;
                padding: 1rem;
            }

            nav {
                flex-direction: row;
                justify-content: center;
                gap: 15px;
            }

            footer {
                position: relative;
                width: 100%;
                left: 0;
            }

            .gallery-grid {
                column-count: 1;
            }

            .gallery-item img, .gallery-item video {
                height: auto;
            }

            .filter-buttons {
                flex-direction: column;
                gap: 10px;
            }

            .lightbox .close-btn {
                top: 10px;
                right: 10px;
            }

            .lightbox .nav-btn {
                font-size: 1rem;
                padding: 5px;
            }

            .lightbox-content .caption {
                font-size: 1rem;
            }

            .admin-controls {
                flex-direction: column;
                gap: 10px;
            }
        }

        @keyframes slideInLeft {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes galleryItemFadeIn {
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes lightboxFadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes captionFadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0% { background: #e0e0e0; }
            50% { background: #d0d0d0; }
            100% { background: #e0e0e0; }
        }
    </style>
</head>
<body>
<header>
    <div class="logo">
        <a href="admin-home.html">
            <img src="MagicNoBackground.png" alt="Magic Sole Logo">
        </a>
    </div>
    <nav>
        <a href="<?php echo dirname($path);?>/admin/admin-home">Admin Home</a>
        <a href="<?php echo dirname($path);?>/admin/view-orders">View Orders</a>
        <a href="<?php echo dirname($path);?>/admin/order-status">Order Status</a>
        <a href="<?php echo dirname($path);?>/admin/admin-gallery">Order Status</a>
        <a href="<?php echo dirname($path);?>/admin/logout">Logout</a>
    </nav>
    <footer>
        <p>© 2025 Magic Sole. All rights reserved.</p>
    </footer>
</header>

<div class="main-content">
    <section class="hero">
        <div class="hero-content">
            <h1>Manage Gallery</h1>
            <p>Add, update, or delete photos and videos showcasing sneaker restorations.</p>
        </div>
    </section>
    <div class="gallery-section">
        <h2>Transformations & Processes</h2>
        <div class="admin-controls">
            <button onclick="openAddModal()">Add Media</button>
        </div>
        <div class="filter-buttons">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="photo">Photos</button>
            <button class="filter-btn" data-filter="video">Videos</button>
        </div>
        <div class="gallery-grid" id="gallery-grid">
            <!-- Gallery items will be populated dynamically -->
        </div>
    </div>
    <div class="lightbox" id="lightbox">
        <span class="close-btn" onclick="closeLightbox()"><i class="fas fa-times"></i></span>
        <span class="nav-btn prev-btn" onclick="changeMedia(-1)"><i class="fas fa-chevron-left"></i></span>
        <div class="lightbox-content" id="lightbox-content"></div>
        <span class="nav-btn next-btn" onclick="changeMedia(1)"><i class="fas fa-chevron-right"></i></span>
    </div>
    <div class="modal" id="add-modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeAddModal()">×</span>
            <h3>Add Media</h3>
            <label for="add-type">Media Type</label>
            <select id="add-type" onchange="updateFileAccept()">
                <option value="photo">Photo</option>
                <option value="video">Video</option>
            </select>
            <label for="add-file">Upload File</label>
            <input type="file" id="add-file" accept="image/jpeg,image/png">
            <label for="add-caption">Caption</label>
            <input type="text" id="add-caption" placeholder="Enter caption">
            <button onclick="addMedia()">Save</button>
        </div>
    </div>
    <div class="modal" id="update-modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeUpdateModal()">×</span>
            <h3>Update Media</h3>
            <label for="update-type">Media Type</label>
            <select id="update-type" onchange="updateFileAccept()">
                <option value="photo">Photo</option>
                <option value="video">Video</option>
            </select>
            <label for="update-file">Upload New File (optional)</label>
            <input type="file" id="update-file" accept="image/jpeg,image/png">
            <label for="update-caption">Caption</label>
            <input type="text" id="update-caption" placeholder="Enter caption">
            <button onclick="updateMedia()">Save</button>
        </div>
    </div>
</div>

<script>

    // In admin-home.html, admin-gallery.php
fetch('check_session.php')
    .then(response => response.json())
    .then(data => {
        if (!data.loggedIn || !data.isAdmin) {
            window.location.href = 'login.php';
        }
    });
    
    // Redirect non-admins to login
    if (!localStorage.getItem('isAdmin')) {
        window.location.href = 'login.html';
    }

    // Load gallery items from localStorage or use default
    const defaultGalleryItems = [
        { type: 'video', src: './sneaker1.mp4', alt: '' },
        { type: 'video', src: './sneaker2.mp4', alt: '' },
        { type: 'video', src: './joey1.mp4', alt: '' },
        { type: 'video', src: './kev1.mp4', alt: '' },
        { type: 'photo', src: './joey2.jpg', alt: '' },
        { type: 'photo', src: './joey3.jpg', alt: '' }
    ];

    function loadGalleryItems() {
        try {
            const items = localStorage.getItem('galleryItems');
            return items ? JSON.parse(items) : [...defaultGalleryItems];
        } catch (e) {
            console.error('Error parsing galleryItems:', e);
            return [...defaultGalleryItems];
        }
    }

    function saveGalleryItems(items) {
        try {
            const serialized = JSON.stringify(items);
            localStorage.setItem('galleryItems', serialized);
            // Verify save by re-parsing
            JSON.parse(localStorage.getItem('galleryItems'));
        } catch (e) {
            console.error('Error saving galleryItems:', e);
            alert('Failed to save media. Try using a smaller file or deleting some items.');
        }
    }

    let galleryItems = loadGalleryItems();
    let currentIndex = 0;
    let currentFilter = 'all';
    let filteredItems = galleryItems;
    let selectedIndex = null;

    // Update file input accept attribute based on media type
    function updateFileAccept() {
        const addType = document.getElementById('add-type').value;
        const updateType = document.getElementById('update-type').value;
        document.getElementById('add-file').accept = addType === 'photo' ? 'image/jpeg,image/png' : 'video/mp4,video/webm';
        document.getElementById('update-file').accept = updateType === 'photo' ? 'image/jpeg,image/png' : 'video/mp4,video/webm';
    }

    function populateGallery(filter = 'all') {
        currentFilter = filter;
        const galleryGrid = document.getElementById('gallery-grid');
        galleryGrid.innerHTML = '';

        for (let i = 0; i < 6; i++) {
            const placeholder = document.createElement('div');
            placeholder.classList.add('gallery-item', 'loading');
            galleryGrid.appendChild(placeholder);
        }

        setTimeout(() => {
            galleryGrid.innerHTML = '';
            filteredItems = filter === 'all' ? galleryItems : galleryItems.filter(item => item.type === filter);

            filteredItems.forEach((item, index) => {
                const galleryItem = document.createElement('div');
                galleryItem.classList.add('gallery-item');
                galleryItem.setAttribute('data-type', item.type);
                const originalIndex = galleryItems.indexOf(item);
                galleryItem.setAttribute('data-original-index', originalIndex);

                const adminActions = `
                    <div class="admin-actions">
                        <button class="admin-btn update-btn" onclick="openUpdateModal(${originalIndex})">Update</button>
                        <button class="admin-btn delete-btn" onclick="deleteMedia(${originalIndex})">Delete</button>
                    </div>
                `;

                if (item.type === 'photo') {
                    galleryItem.innerHTML = `
                        <img src="${item.src}" alt="${item.alt}" loading="lazy">
                        <div class="overlay">
                            <i class="fas fa-search-plus"></i>
                            <span class="caption">${item.alt}</span>
                            ${adminActions}
                        </div>
                    `;
                } else {
                    galleryItem.innerHTML = `
                        <video src="${item.src}" muted loading="lazy"></video>
                        <div class="overlay">
                            <i class="fas fa-play"></i>
                            <span class="caption">${item.alt}</span>
                            ${adminActions}
                        </div>
                    `;
                }

                galleryItem.addEventListener('click', (e) => {
                    if (!e.target.classList.contains('admin-btn')) {
                        openLightbox(originalIndex);
                    }
                });
                galleryGrid.appendChild(galleryItem);
            });
        }, 1000);
    }

    function openLightbox(index) {
        currentIndex = index;
        const item = galleryItems[index];
        const lightboxContent = document.getElementById('lightbox-content');
        lightboxContent.innerHTML = item.type === 'photo' 
            ? `<img src="${item.src}" alt="${item.alt}">
               <div class="caption">${item.alt}</div>`
            : `<video src="${item.src}" controls autoplay></video>
               <div class="caption">${item.alt}</div>`;
        document.getElementById('lightbox').style.display = 'flex';
    }

    function closeLightbox() {
        document.getElementById('lightbox').style.display = 'none';
        const lightboxContent = document.getElementById('lightbox-content');
        if (lightboxContent.querySelector('video')) {
            lightboxContent.querySelector('video').pause();
        }
    }

    function changeMedia(direction) {
        const currentFilteredIndex = filteredItems.indexOf(galleryItems[currentIndex]);
        let newFilteredIndex = currentFilteredIndex + direction;

        if (newFilteredIndex < 0) newFilteredIndex = filteredItems.length - 1;
        if (newFilteredIndex >= filteredItems.length) newFilteredIndex = 0;

        currentIndex = galleryItems.indexOf(filteredItems[newFilteredIndex]);
        openLightbox(currentIndex);
    }

    function openAddModal() {
        document.getElementById('add-type').value = 'photo';
        document.getElementById('add-file').value = '';
        document.getElementById('add-caption').value = '';
        updateFileAccept();
        document.getElementById('add-modal').style.display = 'flex';
    }

    function closeAddModal() {
        document.getElementById('add-modal').style.display = 'none';
    }

    function addMedia() {
        const type = document.getElementById('add-type').value;
        const fileInput = document.getElementById('add-file');
        const caption = document.getElementById('add-caption').value;

        if (!fileInput.files[0] || !caption) {
            alert('Please upload a file and provide a caption.');
            return;
        }

        const file = fileInput.files[0];
        // Validate file type and size
        if (type === 'photo' && !file.type.match(/^image\/(jpeg|png)$/)) {
            alert('Please upload a JPG or PNG image.');
            return;
        }
        if (type === 'video' && !file.type.match(/^video\/(mp4|webm)$/)) {
            alert('Please upload an MP4 or WebM video.');
            return;
        }
        if (file.size > 2 * 1024 * 1024) { // 2 MB limit
            alert('File is too large. Please upload a file smaller than 2 MB.');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const base64Src = e.target.result;
            galleryItems.push({ type, src: base64Src, alt: caption });
            saveGalleryItems(galleryItems);
            populateGallery(currentFilter);
            closeAddModal();
        };

        reader.onerror = function() {
            alert('Error reading file. Please try again.');
        };

        reader.readAsDataURL(file);
    }

    function openUpdateModal(index) {
        selectedIndex = index;
        const item = galleryItems[index];
        document.getElementById('update-type').value = item.type;
        document.getElementById('update-file').value = '';
        document.getElementById('update-caption').value = item.alt;
        updateFileAccept();
        document.getElementById('update-modal').style.display = 'flex';
    }

    function closeUpdateModal() {
        document.getElementById('update-modal').style.display = 'none';
        selectedIndex = null;
    }

    function updateMedia() {
        if (selectedIndex === null) return;

        const type = document.getElementById('update-type').value;
        const fileInput = document.getElementById('update-file');
        const caption = document.getElementById('update-caption').value;

        if (!caption) {
            alert('Please provide a caption.');
            return;
        }

        if (fileInput.files[0]) {
            const file = fileInput.files[0];
            // Validate file type and size
            if (type === 'photo' && !file.type.match(/^image\/(jpeg|png)$/)) {
                alert('Please upload a JPG or PNG image.');
                return;
            }
            if (type === 'video' && !file.type.match(/^video\/(mp4|webm)$/)) {
                alert('Please upload an MP4 or WebM video.');
                return;
            }
            if (file.size > 2 * 1024 * 1024) { // 2 MB limit
                alert('File is too large. Please upload a file smaller than 2 MB.');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const base64Src = e.target.result;
                galleryItems[selectedIndex] = { type, src: base64Src, alt: caption };
                saveGalleryItems(galleryItems);
                populateGallery(currentFilter);
                closeUpdateModal();
            };

            reader.onerror = function() {
                alert('Error reading file. Please try again.');
            };

            reader.readAsDataURL(file);
        } else {
            galleryItems[selectedIndex] = {
                type,
                src: galleryItems[selectedIndex].src,
                alt: caption
            };
            saveGalleryItems(galleryItems);
            populateGallery(currentFilter);
            closeUpdateModal();
        }
    }

    function deleteMedia(index) {
        if (confirm('Are you sure you want to delete this media?')) {
            galleryItems.splice(index, 1);
            saveGalleryItems(galleryItems);
            populateGallery(currentFilter);
        }
    }

    function logout() {
        localStorage.removeItem('isAdmin');
        localStorage.removeItem('clientEmail');
        window.location.href = 'login.html';
    }

    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelector('.filter-btn.active').classList.remove('active');
            btn.classList.add('active');
            const filter = btn.getAttribute('data-filter');
            populateGallery(filter);
        });
    });

    window.addEventListener('scroll', () => {
        const hero = document.querySelector('.hero');
        const scrolled = window.pageYOffset;
        hero.style.setProperty('--scroll', scrolled * 0.5 + 'px');
    });

    populateGallery();
</script>
</body>
</html>