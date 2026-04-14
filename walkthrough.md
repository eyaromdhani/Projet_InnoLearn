# Implémentation des Tests d'Intégration Junit 5

Nous avons implémenté avec succès les tests visant la vraie base de données de manière sécurisée et complète pour les deux services : `ServiceOffreStage` et `ServiceStageCondidature`.

## 🛠 Modèle de Test "Chaîné" (`@Order`)
Afin que les tests ne corrompent pas ou ne modifient pas vos données de production/réelles sans s'abîmer en route, nous avons utilisé un modèle très strict appelé **Test Intégré Séquentiel** grâce à l'annotation JUnit 5 `@Order`.

Le cycle de vie du test pour chaque classe de service (par exemple, [ServiceOffreStageTest.java](file:///d:/JavaESPRIT/innolearn/src/test/java/Services/ServiceOffreStageTest.java)) agit ainsi :
1. **`@Order(1) testAjouter()`** : Insère dans la base de données un élément propre aux tests avec un titre unique `TITRE_TEST_UNITAIRE_UNIQUE`.
2. **`@Order(2) testAfficherAllAndFindId()`** : Appelle la méthode `afficherAll()`, charge la liste, vérifie le contenu, et cherche dans toute cette liste l'offre créée à l'étape 1 pour récupérer son `'ID'`. 
3. **`@Order(3) testGetById()`** : Utilise l'ID trouvé pour appeler la méthode `getById(...)` et vérifie qu'elle retourne bien la bonne offre.
4. **`@Order(4) testModifier()`** : Récupère au travers du service la vue de l'offre grâce à son ID, change son titre en `TITRE_TEST_UNITAIRE_MODIFIE` et met à jour via la méthode `modifier(...)`, tout en vérifiant par la suite.
5. **`@Order(5) testSupprimer()`** : Enfin, appelle la méthode `supprimer(...)` en donnant l'ID. Cette étape est cruciale car elle nettoie complètement la base de données des données du test !

Ainsi, vous lancez les tests, le programme passe par les 5 méthodes pour un processus complet : **Création -> Lecture Globale -> Lecture Unique -> Modification -> Suppression**, tout en gardant votre base saine à la fin !

> [!TIP]
> Vous pouvez maintenant ouvrir votre IntelliJ ou votre IDE favori, faire un clic droit sur vos classes de test, puis cliquer sur **"Run Tests"** (Flèche verte de JUnit) et voir tous vos tests au vert sans risques pour vos données métier !
