<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Utilisateur;
use App\Models\InscriptionEtudiant;
use App\Models\StageEffectue;
use App\Models\Evaluation;
use App\Models\DepotRapport;
// Removed CompteRendu import as getComptesRendus is being removed
use App\Models\Messagerie; // Added import
use PDO;

/**
 * Class Etudiant
 *
 * Represents the etudiant table.
 *
 * @package App\Models
 */
class Etudiant extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'etudiant';

    /**
     * @var string The ID of the student. This is also a foreign key to utilisateur.id.
     */
    public string $id_utilisateur;

    // Removed ine, nom, prenom, date_naissance, lieu_naissance, contact properties

    /**
     * Etudiant constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the student (references utilisateur.id).
     * @return string
     */
    public function getIdUtilisateur(): string
    {
        return $this->id_utilisateur;
    }

    /**
     * Sets the ID of the student.
     * @param string $id_utilisateur
     */
    public function setIdUtilisateur(string $id_utilisateur): void
    {
        $this->id_utilisateur = $id_utilisateur;
        // This is the PK for this table.
    }

    // Removed getters and setters for ine, nom, prenom, date_naissance, lieu_naissance, contact

    /**
     * Gets the related Utilisateur for this Etudiant.
     * This is based on id_utilisateur which is the PK for this table and FK to Utilisateur.
     * @return Utilisateur|null
     */
    public function getUtilisateur(): ?Utilisateur
    {
        if (empty($this->id_utilisateur)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id = :id");
        $stmt->bindParam(':id', $this->id_utilisateur);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $utilisateur = new Utilisateur($this->pdo);
        $utilisateur->id = $data['id'];
        $utilisateur->nom_utilisateur = $data['nom_utilisateur'];
        $utilisateur->mot_de_passe = $data['mot_de_passe'];
        $utilisateur->email = $data['email'] ?? null;
        $utilisateur->date_creation_compte = $data['date_creation_compte'] ?? null;
        $utilisateur->date_derniere_connexion = $data['date_derniere_connexion'] ?? null;
        $utilisateur->type_utilisateur_id = $data['type_utilisateur_id'] ?? null;
        $utilisateur->groupe_utilisateur_id = $data['groupe_utilisateur_id'] ?? null;
        $utilisateur->niveau_acces_donnees_id = $data['niveau_acces_donnees_id'] ?? null;
        return $utilisateur;
    }

    /**
     * Gets all InscriptionEtudiant records for this Etudiant.
     *
     * @return InscriptionEtudiant[]
     */
    public function getInscriptionsEtudiant(): array
    {
        if (empty($this->id_utilisateur)) { // PK of Etudiant is id_utilisateur
            return [];
        }

        // FK in inscription_etudiant is id_etudiant which refers to etudiant.id_utilisateur
        $stmt = $this->pdo->prepare("SELECT * FROM inscription_etudiant WHERE id_etudiant = :id_etudiant");
        $stmt->bindParam(':id_etudiant', $this->id_utilisateur);
        $stmt->execute();
        $inscriptionsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $inscriptions = [];
        if ($inscriptionsData) {
            foreach ($inscriptionsData as $data) {
                $inscription = new InscriptionEtudiant($this->pdo);
                $inscription->id_etudiant = $data['id_etudiant'];
                $inscription->id_annee_academique = $data['id_annee_academique'];
                $inscription->id_niveau_etude = $data['id_niveau_etude'];
                $inscription->date_inscription = $data['date_inscription'] ?? null;
                $inscription->statut_inscription = $data['statut_inscription'] ?? null;
                $inscriptions[] = $inscription;
            }
        }
        return $inscriptions;
    }

    /**
     * Gets all StageEffectue records for this Etudiant.
     *
     * @return StageEffectue[]
     */
    public function getStagesEffectues(): array
    {
        if (empty($this->id_utilisateur)) { // PK of Etudiant is id_utilisateur
            return [];
        }

        // FK in stage_effectue is utilisateur_id which refers to etudiant.id_utilisateur
        $stmt = $this->pdo->prepare("SELECT * FROM stage_effectue WHERE utilisateur_id = :utilisateur_id");
        $stmt->bindParam(':utilisateur_id', $this->id_utilisateur);
        $stmt->execute();
        $stagesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stages = [];
        if ($stagesData) {
            foreach ($stagesData as $data) {
                $stage = new StageEffectue($this->pdo);
                // Populate StageEffectue based on its corrected properties
                $stage->utilisateur_id = $data['utilisateur_id'];
                $stage->entreprise_id = $data['entreprise_id'];
                $stage->date_debut = $data['date_debut'] ?? null;
                $stage->date_fin = $data['date_fin'] ?? null;
                $stages[] = $stage;
            }
        }
        return $stages;
    }

    /**
     * Gets all Evaluation records for this Etudiant.
     *
     * @return Evaluation[]
     */
    public function getEvaluationsRecues(): array
    {
        if (empty($this->id_utilisateur)) { // PK of Etudiant is id_utilisateur
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM evaluation WHERE etudiant_id = :etudiant_id");
        $stmt->bindParam(':etudiant_id', $this->id_utilisateur);
        $stmt->execute();
        $evaluationsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $evaluations = [];
        if ($evaluationsData) {
            foreach ($evaluationsData as $data) {
                $evaluation = new Evaluation($this->pdo);
                // Correct hydration for Evaluation object
                $evaluation->enseignant_id = $data['enseignant_id'];
                $evaluation->etudiant_id = $data['etudiant_id'];
                $evaluation->ecue_id = $data['ecue_id'];
                $evaluation->note = isset($data['note']) ? (float)$data['note'] : null;
                $evaluation->date_evaluation = $data['date_evaluation'] ?? null;
                $evaluations[] = $evaluation;
            }
        }
        return $evaluations;
    }

    /**
     * Gets all DepotRapport records for this Etudiant.
     *
     * @return DepotRapport[]
     */
    public function getDepotsRapport(): array
    {
        if (empty($this->id_utilisateur)) { // PK of Etudiant is id_utilisateur
            return [];
        }

        // FK in depot_rapport is utilisateur_id which refers to etudiant.id_utilisateur
        $stmt = $this->pdo->prepare("SELECT * FROM depot_rapport WHERE utilisateur_id = :utilisateur_id_val");
        $stmt->bindParam(':utilisateur_id_val', $this->id_utilisateur);
        $stmt->execute();
        $depotsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $depots = [];
        if ($depotsData) {
            foreach ($depotsData as $data) {
                $depot = new DepotRapport($this->pdo);
                // Populate DepotRapport based on its corrected properties
                $depot->utilisateur_id = $data['utilisateur_id']; // Corrected FK name
                $depot->rapport_etudiant_id = $data['rapport_etudiant_id'];
                $depot->date_depot = $data['date_depot'] ?? null;
                $depots[] = $depot;
            }
        }
        return $depots;
    }
    // getComptesRendus was removed in a previous step, this is just context.

    /**
     * Gets all Messagerie records where this Etudiant is the etudiant_concerne.
     *
     * @return Messagerie[]
     */
    public function getMessagesMessagerieConcerne(): array
    {
        if (empty($this->id_utilisateur)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM messagerie WHERE etudiant_concerne_id = :etudiant_id");
        $stmt->bindParam(':etudiant_id', $this->id_utilisateur);
        $stmt->execute();
        $messagesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $messages = [];
        if ($messagesData) {
            foreach ($messagesData as $data) {
                $message = new Messagerie($this->pdo);
                $message->membre_commission_id = $data['membre_commission_id'];
                $message->etudiant_concerne_id = $data['etudiant_concerne_id'];
                $message->discussion_id = $data['discussion_id'];
                $message->message = $data['message'] ?? null;
                $message->date_message = $data['date_message'];
                $messages[] = $message;
            }
        }
        return $messages;
    }
}
