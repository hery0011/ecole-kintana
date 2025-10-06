<?php
// Mettez ceci au tout début du fichier index.php pour gérer les sessions correctement
session_start();

// Le reste de votre code PHP (chargement des données) est déjà présent plus bas.
// Nul besoin de le répéter ici.
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>École | Accueil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="main-header">
        <div class="container">
            <h1>🏫 Nom de l'École</h1>
            <nav>
                <ul>
                    <li><a href="index.html">Accueil</a></li>
                    <li><a href="#actualites">Actualités</a></li>
                    <li><a href="#evenements">Événements</a></li>
                    <li><a href="#presentation">Présentation</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="login.php" class="login-btn">Admin</a></li> 
                </ul>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <h2>Bienvenue sur le site de l'École !</h2>
            <p>Notre engagement : l'excellence, le bien-être et l'épanouissement de chaque élève.</p>
            <a href="#presentation" class="button">Découvrir l'école</a>
        </div>
    </section>

    <section id="mot-directrice" class="director-message">
        <div class="container">
            <h2>Mot de la Directrice</h2>
            <div class="message-content">
                <img src="sary/directrice.jpg" alt="Photo de la Directrice, Madame Dupont" class="director-photo">
                <div class="text-content">
                    <p>Cher(ère)s parents, cher(ère)s élèves,</p>
                    <p>C'est avec une immense joie que je vous accueille sur notre site web. Notre établissement est un lieu d'apprentissage et d'épanouissement, où chaque élève est encouragé à donner le meilleur de lui-même.</p>
                    <p>Nous nous engageons à offrir un environnement stimulant, sécurisé et bienveillant, favorisant l'excellence académique et le développement personnel. Je vous invite à parcourir nos actualités et à participer activement à la vie de notre communauté.</p>
                    <p class="signature">Mme. Émilie Dupont</p>
                    <p class="role">Directrice de l'École</p>
                </div>
            </div>
        </div>
    </section>

    <section id="actualites" class="news-section">
        <div class="container">
            <h2>Dernières Actualités 📣</h2>

            <?php
            // Lire les données des actualités
            $news_data = file_get_contents('data/news.json');
            $news = json_decode($news_data, true);

            // Afficher seulement les 3 dernières (ou toutes, selon le besoin)
            $news_to_display = array_slice($news, 0, 3);

            if (!empty($news_to_display)):
                foreach ($news_to_display as $item):
                    // Détermine si l'article est urgent
                    $class = $item['urgent'] ? 'news-item urgent' : 'news-item';
            ?>
            <article class="<?php echo $class; ?>">
                <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                <p class="date">Publié le <?php echo htmlspecialchars($item['date']); ?></p>
                <p><?php echo nl2br(htmlspecialchars($item['content'])); ?></p>
                <?php if (!empty($item['link'])): ?>
                    <a href="<?php echo htmlspecialchars($item['link']); ?>">Lire la suite</a>
                <?php endif; ?>
            </article>
            <?php
                endforeach;
            else:
            ?>
            <p style="text-align: center;">Aucune actualité n'est disponible pour le moment.</p>
            <?php
            endif;
            ?>

        </div>
    </section>

    <section id="evenements" class="events-section">
        <div class="container">
            <h2>Prochains Événements 🎉</h2>
            
            <div class="events-grid">
                
                <?php
                // Lire les données des événements
                $events_data = file_get_contents('data/event.json');
                $events = json_decode($events_data, true);

                if (!empty($events)):
                    foreach ($events as $event):
                ?>
                <div class="event-card">
                    <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                    <p>🗓️ **Date:** <?php echo htmlspecialchars($event['date']); ?></p>
                    <?php if (!empty($event['time'])): ?>
                        <p>🕒 **Heure:** <?php echo htmlspecialchars($event['time']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($event['location'])): ?>
                        <p>📍 **Lieu:** <?php echo htmlspecialchars($event['location']); ?></p>
                    <?php endif; ?>
                    <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                </div>
                <?php
                    endforeach;
                else:
                ?>
                <p style="text-align: center; width: 100%;">Aucun événement n'est programmé pour le moment.</p>
                <?php
                endif;
                ?>

            </div>
            
        </div>
    </section>

    <section id="presentation" class="about-section">
        <div class="container">
            <h2>Qui Sommes-Nous ? 🌟</h2>
            
            <div class="presentation-grid">
                
                <div class="description-text">
                    <p class="mission-statement">Depuis **1980**, l'École est au cœur de l'éducation. Notre engagement est de bâtir un environnement propice à l'**excellence académique** et à l'**épanouissement personnel** de chaque élève.</p>
                    
                    <h3>Notre Mission</h3>
                    
                    <div class="value-cards">
                        <div class="value-item">
                            <h4><i class="icon">💡</i> Développement</h4>
                            <p>Cultiver l'esprit critique, la curiosité et la créativité chez nos élèves.</p>
                        </div>
                        <div class="value-item">
                            <h4><i class="icon">🤝</i> Communauté</h4>
                            <p>Promouvoir le respect, l'entraide et l'engagement citoyen au sein de l'école.</p>
                        </div>
                        <div class="value-item">
                            <h4><i class="icon">🎯</i> Excellence</h4>
                            <p>Assurer des programmes pédagogiques de haut niveau pour la réussite de tous.</p>
                        </div>
                    </div>
                    
                </div>
                
                <div class="stats-card-professional">
                    <p>Fondée il y a plus de 40 ans, notre école est un acteur majeur de l'éducation dans la région. Voici un aperçu de notre engagement en chiffres :</p>
                    
                    <div class="stats-pro">
                        <div class="stat-item-pro">
                            <h3>350+</h3>
                            <p>Élèves Inscrits</p>
                        </div>
                        <div class="stat-item-pro">
                            <h3>30</h3>
                            <p>Professeurs Experts</p>
                        </div>
                        <div class="stat-item-pro">
                            <h3>95%</h3>
                            <p>Taux de Réussite</p>
                        </div>
                        <div class="stat-item-pro">
                            <h3>40+</h3>
                            <p>Années d'Expérience</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer id="contact" class="main-footer">
        <div class="container">
            <p>**Contactez-nous**</p>
            <p>📍 Adresse : 123 Rue de l'Éducation, Ville</p>
            <p>📞 Téléphone : 01 23 45 67 89 | 📧 Email : contact@ecole.com</p>
            <p class="copyright">&copy; 2024 Nom de l'École. Tous droits réservés.</p>
        </div>
    </footer>

</body>
</html>