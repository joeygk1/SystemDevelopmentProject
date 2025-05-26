<?php

$path = $_SERVER['SCRIPT_NAME'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Magic Sole</title>
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
            height: 95vh;
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
            overflow-y: auto;
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
            background: url('<?php echo dirname($path);?>/Images/CoolGrey.gif') no-repeat center center/cover;
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
            width: 100%;
            height: 200px;
        }

        .gallery-item:nth-child(1) { animation-delay: 0.6s; }
        .gallery-item:nth-child(2) { animation-delay: 0.7s; }
        .gallery-item:nth-child(3) { animation-delay: 0.8s; }
        .gallery-item:nth-child(4) { animation-delay: 0.9s; }
        .gallery-item:nth-child(5) { animation-delay: 1.0s; }
        .gallery-item:nth-child(6) { animation-delay: 1.1s; }

        .gallery-item img, .gallery-item video {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
            height: 200px;
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

            .gallery-item {
                height: 150px;
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
        <a href="<?php echo dirname($path);?>/client/home">
            <img src="<?php echo dirname($path);?>/Images/MagicNoBackground.png" alt="Magic Sole Logo">
        </a>
    </div>
    <nav>
        <a href="<?php echo dirname($path);?>/client/home">Home</a>
        <a href="<?php echo dirname($path);?>/client/services">Services</a>
        <a href="<?php echo dirname($path);?>/client/about">About</a>
        <a href="<?php echo dirname($path);?>/client/policies">Policies</a>
        <a href="<?php echo dirname($path);?>/booking/booking">Booking</a>
        <a href="<?php echo dirname($path);?>/client/gallery">Gallery</a>
        <a href="<?php echo dirname($path);?>/client/help" target="_blank">Help</a>
        <?php
        if (!isset($_SESSION['token'])) {
            ?>
            <a href="<?php echo dirname($path);?>/client/login">Login</a>
            <a href="<?php echo dirname($path);?>/client/register">Register</a>
            <?php
        } else {
            ?>
            <a href="<?php echo dirname($path);?>/client/client-orders">Orders</a>
            <a href="<?php echo dirname($path);?>/client/logout">Logout</a>
            <?php
        }
        ?>
    </nav>
    <footer>
        <p>© 2025 Magic Sole. All rights reserved.</p>
    </footer>
</header>

<div class="main-content">
    <section class="hero">
        <div class="hero-content">
            <h1>Our Gallery</h1>
            <p>Explore the magic of sneaker restoration through our photo and video gallery!</p>
        </div>
    </section>
    <div class="gallery-section">
        <h2>Transformations & Processes</h2>
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
</div>

<script>
    // Default gallery items
    const defaultGalleryItems = [
    { type: 'video', src: '/MagicSoleProject/Videos/sneaker1.mp4', alt: 'Sneaker Restoration 1' },
    { type: 'video', src: '/MagicSoleProject/Videos/sneaker2.mp4', alt: 'Sneaker Restoration 2' },
    { type: 'video', src: '/MagicSoleProject/Videos/joey1.mp4', alt: 'Joey\'s Sneaker Process' },
    { type: 'video', src: '/MagicSoleProject/Videos/kev1.mp4', alt: 'Kev\'s Sneaker Process' },
    { type: 'photo', src: '/MagicSoleProject/Images/joey2.jpg', alt: 'Restored Sneaker Photo 1' },
    { type: 'photo', src: '/MagicSoleProject/Images/joey3.jpg', alt: 'Restored Sneaker Photo 2' }
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

    let galleryItems = loadGalleryItems();
    let currentIndex = 0;
    let currentFilter = 'all';
    let filteredItems = galleryItems;

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

                if (item.type === 'photo') {
                    galleryItem.innerHTML = `
                        <img src="${item.src}" alt="${item.alt}" loading="lazy">
                        <div class="overlay">
                            <i class="fas fa-search-plus"></i>
                            <span class="caption">${item.alt}</span>
                        </div>
                    `;
                } else {
                    galleryItem.innerHTML = `
                        <video src="${item.src}" muted loading="lazy"></video>
                        <div class="overlay">
                            <i class="fas fa-play"></i>
                            <span class="caption">${item.alt}</span>
                        </div>
                    `;
                }

                galleryItem.addEventListener('click', () => openLightbox(originalIndex));
                galleryGrid.appendChild(galleryItem);
            });
        }, 1000);
    }

    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelector('.filter-btn.active').classList.remove('active');
            btn.classList.add('active');
            const filter = btn.getAttribute('data-filter');
            populateGallery(filter);
        });
    });

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

    window.addEventListener('scroll', () => {
        const hero = document.querySelector('.hero');
        const scrolled = window.pageYOffset;
        hero.style.setProperty('--scroll', scrolled * 0.5 + 'px');
    });

    populateGallery();
</script>
</body>
</html>