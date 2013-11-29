# Hello, I'm Yuki

Yuki est un Manga Grabber, un outils qui vous permet de télécharger les scans de vos mangas sur votre ordinateur.


## Installation

Yuki s'utilise en ligne de commande (par souci de simplicité et de performance) et nécessite d'avoir PHP5.4 installé sur votre environnement.
Copiez l'ensemble des sources dans l'un de vos dossier et ouvrez-y un terminal.
Par défaut, Yuki utilise MangaEden comme API (voir section "extension" pour implemter votre propre API).


## Utilisation

### Afficher la liste des mangas

```
php yuki list
```

Affichera :

```
4e70ea03c092255ef70046f8	K-On!
4e70e91ec092255ef7001773	Ultrared
4e70e921c092255ef7001845	Furinkazan
4e70e922c092255ef7001880	Bleach Art Books
4e70e923c092255ef70018db	Hana Otoko
4e70e924c092255ef70018eb	G Senjou Heavens Door
4e70e924c092255ef70018f3	Hot Man
4e70e924c092255ef70018f9	Aiotomenaide
4e70e924c092255ef700190d	Sugarpot
4e70e924c092255ef700191d	Wolfandspice
4e70e924c092255ef7001927	Gods Left Hand, Devils Right Hand
4e70e925c092255ef7001960	Ushiototora
...
```

Le 1e élement est l'id fournit par l'API, nécessaire par la suite pour télécharger le manga.
Le 2e est son nom.

### Rechercher un manga

```
php yuki find "Naruto"
```

Affichera :

```
4e70e927c092255ef70019ca	Naruto Ng
5010c709c09225527e000444	Road To Naruto The Movie
4e70ea03c092255ef70046f0	Naruto
```

### Télécharger un manga

Une fois votre manga et son id trouvé, il suffit d'utilisé la commande suivante :

```
php yuki grab 4e70ea03c092255ef70046f0
```

L'id passé correspond au manga que vous souhaitez télécharger.
Yuki vous indiquera la progression du téléchargement (notez que cette tâche peut prendre beaucoup de temps suivant le nombre de chapitres).

Une fois le téléchargement terminé, vous pourrez accéder à votre manga dans le dossier `/mangas/`.


## Extension

### Changer le dossier de téléchargement

Si vous souhaitez changer de dossier de téléchargement, vous devez modifier la ligne suivante dans le fichier `yuki` :

```php
$grabber = new MangaGrabber($provider, '/you/new/path/');
```

### Votre API

Yuki charge par défaut l'API de MangaEden, si vous souhaitez utiliser la votre, il vous suffit de créer une classe implémentant `grabber\MangaProvider` :

```php
namespace grabber;

interface MangaProvider
{

	public function getMangas();

	public function getManga($mangaId);

	public function getChapters($mangaId);

	public function getPages($mangaId, $chapterId);

}
```

Et de la passer à l'objet MangaGrabber dans le fichier `yuki` :

```php
$provider = new my\provider\MyProvider();
$grabber = new MangaGrabber($provider, '/you/new/path/');
```

Voir la classe `grabber\provider\MangaEdenProvider` pour un exemple concret.

## Et voilà :)