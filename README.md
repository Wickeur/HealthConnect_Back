# PROJET REALISEE PAR : LEULIET Quentin & WICKE Julian
# API : HealthConnect_Back


## /role

GET /role/{id}: Récupérer les détails d'un rôle spécifique.
Attendu : L'ID du rôle dans l'URL.

POST /role: Créer un nouveau rôle.
Attendu : Les données du rôle dans le corps de la requête au format JSON.


## /roles

GET /roles: Récupérer la liste de tous les rôles.
Attendu : Aucune donnée attendue.

## /user

GET /user/{id}: Récupérer les détails d'un utilisateur spécifique.
Attendu : L'ID de l'utilisateur dans l'URL.

POST /user: Créer un nouvel utilisateur.
Attendu : Les données de l'utilisateur dans le corps de la requête au format JSON.

PUT /user: Mettre à jour les informations d'un utilisateur existant.
Attendu : Les nouvelles données de l'utilisateur dans le corps de la requête au format JSON, y compris l'ID de l'utilisateur.

## /users

GET /users: Récupérer la liste de tous les utilisateurs.
Attendu : Aucune donnée attendue.

## /medicalFile

GET /medicalFile/{idUser}: Récupérer le dossier médical d'un utilisateur spécifique.
Attendu : L'ID de l'utilisateur dans l'URL.

POST /medicalFile: Créer un nouveau dossier médical.
Attendu : Les données du dossier médical dans le corps de la requête au format JSON.

PUT /medicalFile: Mettre à jour le dossier médical d'un utilisateur.
Attendu : Les nouvelles données du dossier médical dans le corps de la requête au format JSON, y compris l'ID de l'utilisateur.

## /RDV

GET /RDV/{id}: Récupérer les détails d'un rendez-vous spécifique.
Attendu : L'ID du rendez-vous dans l'URL.

POST /RDV: Créer un nouveau rendez-vous.
Attendu : Les données du rendez-vous dans le corps de la requête au format JSON.

PUT /RDV: Mettre à jour les informations d'un rendez-vous existant.
Attendu : Les nouvelles données du rendez-vous dans le corps de la requête au format JSON, y compris l'ID du rendez-vous.
