<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\GroupeUtilisateur;
use App\Models\TypeUtilisateur;
use App\Models\NiveauAccesDonnees;
use App\Models\Audit;
use App\Models\PersonnelAdministratif;
use App\Models\Enseignant;
use App\Models\Etudiant;
use App\Models\AccesTraitement;
use App\Models\Notification; // Added import
use PDO;

/**
 * Class Utilisateur
 *
 * Represents the utilisateur table.
 *
 * @package App\Models
 */
class Utilisateur extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'utilisateur';

    /**
     * @var string The ID of the user.
     */
    public string $id;

    /**
     * @var string The username.
     */
    public string $nom_utilisateur;

    /**
     * @var string The password hash.
     */
    public string $mot_de_passe;

    /**
     * @var string|null The email address.
     */
    public ?string $email;

    /**
     * @var string|null The creation date of the user account.
     */
    public ?string $date_creation_compte; // Assuming DATETIME or TIMESTAMP

    /**
     * @var string|null The last login date.
     */
    public ?string $date_derniere_connexion; // Assuming DATETIME or TIMESTAMP

    /**
     * @var string|null The ID of the user type this user belongs to.
     */
    public ?string $type_utilisateur_id;

    /**
     * @var string|null The ID of the user group this user belongs to.
     */
    public ?string $groupe_utilisateur_id;

    /**
     * @var string|null The ID of the data access level for this user.
     */
    public ?string $niveau_acces_donnees_id;

    /**
     * Utilisateur constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the user.
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the ID of the user.
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the username.
     * @return string
     */
    public function getNomUtilisateur(): string
    {
        return $this->nom_utilisateur;
    }

    /**
     * Sets the username.
     * @param string $nom_utilisateur
     */
    public function setNomUtilisateur(string $nom_utilisateur): void
    {
        $this->nom_utilisateur = $nom_utilisateur;
    }

    /**
     * Gets the password hash.
     * @return string
     */
    public function getMotDePasse(): string
    {
        return $this->mot_de_passe;
    }

    /**
     * Sets the password hash.
     * @param string $mot_de_passe
     */
    public function setMotDePasse(string $mot_de_passe): void
    {
        $this->mot_de_passe = $mot_de_passe;
    }

    /**
     * Gets the email address.
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Sets the email address.
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * Gets the creation date of the user account.
     * @return string|null
     */
    public function getDateCreationCompte(): ?string
    {
        return $this->date_creation_compte;
    }

    /**
     * Sets the creation date of the user account.
     * @param string|null $date_creation_compte
     */
    public function setDateCreationCompte(?string $date_creation_compte): void
    {
        $this->date_creation_compte = $date_creation_compte;
    }

    /**
     * Gets the last login date.
     * @return string|null
     */
    public function getDateDerniereConnexion(): ?string
    {
        return $this->date_derniere_connexion;
    }

    /**
     * Sets the last login date.
     * @param string|null $date_derniere_connexion
     */
    public function setDateDerniereConnexion(?string $date_derniere_connexion): void
    {
        $this->date_derniere_connexion = $date_derniere_connexion;
    }

    /**
     * Gets the ID of the user type.
     * @return string|null
     */
    public function getTypeUtilisateurId(): ?string
    {
        return $this->type_utilisateur_id;
    }

    /**
     * Sets the ID of the user type.
     * @param string|null $type_utilisateur_id
     */
    public function setTypeUtilisateurId(?string $type_utilisateur_id): void
    {
        $this->type_utilisateur_id = $type_utilisateur_id;
    }

    /**
     * Gets the ID of the user group.
     * @return string|null
     */
    public function getGroupeUtilisateurId(): ?string
    {
        return $this->groupe_utilisateur_id;
    }

    /**
     * Sets the ID of the user group.
     * @param string|null $groupe_utilisateur_id
     */
    public function setGroupeUtilisateurId(?string $groupe_utilisateur_id): void
    {
        $this->groupe_utilisateur_id = $groupe_utilisateur_id;
    }

    /**
     * Gets the ID of the data access level.
     * @return string|null
     */
    public function getNiveauAccesDonneesId(): ?string
    {
        return $this->niveau_acces_donnees_id;
    }

    /**
     * Sets the ID of the data access level.
     * @param string|null $niveau_acces_donnees_id
     */
    public function setNiveauAccesDonneesId(?string $niveau_acces_donnees_id): void
    {
        $this->niveau_acces_donnees_id = $niveau_acces_donnees_id;
    }

    /**
     * Gets the related GroupeUtilisateur.
     * @return GroupeUtilisateur|null
     */
    public function getGroupeUtilisateur(): ?GroupeUtilisateur
    {
        if (empty($this->groupe_utilisateur_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM groupe_utilisateur WHERE id = :id");
        $stmt->bindParam(':id', $this->groupe_utilisateur_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $groupe = new GroupeUtilisateur($this->pdo);
        $groupe->id = $data['id'];
        $groupe->libelle = $data['libelle'];
        $groupe->description = $data['description'] ?? null;
        return $groupe;
    }

    /**
     * Gets the related TypeUtilisateur.
     * @return TypeUtilisateur|null
     */
    public function getTypeUtilisateur(): ?TypeUtilisateur
    {
        if (empty($this->type_utilisateur_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM type_utilisateur WHERE id = :id");
        $stmt->bindParam(':id', $this->type_utilisateur_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $type = new TypeUtilisateur($this->pdo);
        $type->id = $data['id'];
        $type->libelle = $data['libelle'];
        $type->categorie_utilisateur_id = $data['categorie_utilisateur_id'] ?? null;
        return $type;
    }

    /**
     * Gets the related NiveauAccesDonnees.
     * @return NiveauAccesDonnees|null
     */
    public function getNiveauAccesDonnees(): ?NiveauAccesDonnees
    {
        if (empty($this->niveau_acces_donnees_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM niveau_acces_donnees WHERE id = :id");
        $stmt->bindParam(':id', $this->niveau_acces_donnees_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $niveau = new NiveauAccesDonnees($this->pdo);
        $niveau->id = $data['id'];
        $niveau->libelle = $data['libelle'];
        $niveau->description = $data['description'] ?? null;
        return $niveau;
    }

    /**
     * Gets all Audit records related to this Utilisateur.
     *
     * @return Audit[]
     */
    public function getAudits(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM audit WHERE id_utilisateur = :utilisateur_id");
        $stmt->bindParam(':utilisateur_id', $this->id);
        $stmt->execute();
        $auditsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $audits = [];
        if ($auditsData) {
            foreach ($auditsData as $data) {
                $audit = new Audit($this->pdo);
                $audit->id = $data['id'];
                $audit->utilisateur_id = $data['id_utilisateur'] ?? null;
                $audit->action_id = $data['action_id'] ?? null;
                $audit->traitement_id = $data['traitement_id'] ?? null;
                $audit->action_effectuee = $data['action_effectuee'] ?? null;
                $audit->timestamp_action = $data['timestamp_action'] ?? null;
                $audit->adresse_ip = $data['adresse_ip'] ?? null;
                $audits[] = $audit;
            }
        }
        return $audits;
    }

    /**
     * Gets the related PersonnelAdministratif profile for this Utilisateur.
     * @return PersonnelAdministratif|null
     */
    public function getPersonnelAdministratif(): ?PersonnelAdministratif
    {
        // Assumes $this->id is the utilisateur_id to look for in personnel_administratif
        $stmt = $this->pdo->prepare("SELECT * FROM personnel_administratif WHERE id_utilisateur = :id");
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $personnel = new PersonnelAdministratif($this->pdo);
        $personnel->id_utilisateur = $data['id_utilisateur'];
        $personnel->nom = $data['nom'];
        $personnel->prenom = $data['prenom'];
        $personnel->contact = $data['contact'] ?? null;
        $personnel->id_fonction = $data['id_fonction'] ?? null;
        return $personnel;
    }

    /**
     * Gets the related Enseignant profile for this Utilisateur.
     * @return Enseignant|null
     */
    public function getEnseignant(): ?Enseignant
    {
        $stmt = $this->pdo->prepare("SELECT * FROM enseignant WHERE id_utilisateur = :id");
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $enseignant = new Enseignant($this->pdo);
        $enseignant->id_utilisateur = $data['id_utilisateur'];
        $enseignant->nom = $data['nom'];
        $enseignant->prenom = $data['prenom'];
        $enseignant->contact = $data['contact'] ?? null;
        $enseignant->specialite = $data['specialite'] ?? null;
        $enseignant->id_grade = $data['id_grade'] ?? null;
        return $enseignant;
    }

    /**
     * Gets the related Etudiant profile for this Utilisateur.
     * @return Etudiant|null
     */
    public function getEtudiant(): ?Etudiant
    {
        $stmt = $this->pdo->prepare("SELECT * FROM etudiant WHERE id_utilisateur = :id");
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $etudiant = new Etudiant($this->pdo);
        $etudiant->id_utilisateur = $data['id_utilisateur'];
        $etudiant->ine = $data['ine'];
        $etudiant->nom = $data['nom'];
        $etudiant->prenom = $data['prenom'];
        $etudiant->date_naissance = $data['date_naissance'] ?? null;
        $etudiant->lieu_naissance = $data['lieu_naissance'] ?? null;
        $etudiant->contact = $data['contact'] ?? null;
        return $etudiant;
    }

    /**
     * Gets all AccesTraitement records for this Utilisateur.
     *
     * @return AccesTraitement[]
     */
    public function getAccesTraitements(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM acces_traitement WHERE utilisateur_id = :utilisateur_id");
        $stmt->bindParam(':utilisateur_id', $this->id);
        $stmt->execute();
        $accesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $accesItems = [];
        if ($accesData) {
            foreach ($accesData as $data) {
                $acces = new AccesTraitement($this->pdo);
                $acces->traitement_id = $data['traitement_id'];
                $acces->utilisateur_id = $data['utilisateur_id'];
                $acces->date_accesion = $data['date_accesion'] ?? null;
                $acces->heure_accesion = $data['heure_accesion'] ?? null;
                $accesItems[] = $acces;
            }
        }
        return $accesItems;
    }

    /**
     * Gets all Notification records sent by this Utilisateur.
     *
     * @return Notification[]
     */
    public function getNotificationsEmises(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM notification WHERE emetteur_id = :emetteur_id");
        $stmt->bindParam(':emetteur_id', $this->id);
        $stmt->execute();
        $notificationsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $notifications = [];
        if ($notificationsData) {
            foreach ($notificationsData as $data) {
                $notification = new Notification($this->pdo);
                $notification->emetteur_id = $data['emetteur_id'];
                $notification->recepteur_id = $data['recepteur_id'];
                $notification->message = $data['message'] ?? null;
                $notification->date_notification = $data['date_notification'];
                $notifications[] = $notification;
            }
        }
        return $notifications;
    }

    /**
     * Gets all Notification records received by this Utilisateur.
     *
     * @return Notification[]
     */
    public function getNotificationsRecues(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM notification WHERE recepteur_id = :recepteur_id");
        $stmt->bindParam(':recepteur_id', $this->id);
        $stmt->execute();
        $notificationsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $notifications = [];
        if ($notificationsData) {
            foreach ($notificationsData as $data) {
                $notification = new Notification($this->pdo);
                $notification->emetteur_id = $data['emetteur_id'];
                $notification->recepteur_id = $data['recepteur_id'];
                $notification->message = $data['message'] ?? null;
                $notification->date_notification = $data['date_notification'];
                $notifications[] = $notification;
            }
        }
        return $notifications;
    }
}
