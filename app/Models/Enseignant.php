<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Utilisateur;
// Removed StageEffectue import as getStagesEncadres is being removed
use App\Models\Evaluation;
use App\Models\ValidationRapport;
use App\Models\AffectationEncadrant;
use App\Models\HistoriqueFonction;
use App\Models\HistoriqueGrade;
use App\Models\RemiseCompteRendu;
use App\Models\Messagerie; // Added import
use PDO;

/**
 * Class Enseignant
 *
 * Represents the enseignant table.
 *
 * @package App\Models
 */
class Enseignant extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'enseignant';

    /**
     * @var string The ID of the teacher. This is also a foreign key to utilisateur.id.
     */
    public string $id_utilisateur;

    // Removed nom, prenom, contact, specialite, id_grade properties

    /**
     * Enseignant constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the teacher (references utilisateur.id).
     * @return string
     */
    public function getIdUtilisateur(): string
    {
        return $this->id_utilisateur;
    }

    /**
     * Sets the ID of the teacher.
     * @param string $id_utilisateur
     */
    public function setIdUtilisateur(string $id_utilisateur): void
    {
        $this->id_utilisateur = $id_utilisateur;
        // This is the PK for this table.
    }

    // Removed getters and setters for nom, prenom, contact, specialite, id_grade

    /**
     * Gets the related Utilisateur for this Enseignant.
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
        $utilisateur->type_utilisateur_id = $data['id_type_utilisateur'] ?? null;
        $utilisateur->groupe_utilisateur_id = $data['groupe_utilisateur_id'] ?? null;
        $utilisateur->niveau_acces_donnees_id = $data['niveau_acces_donnees_id'] ?? null;
        return $utilisateur;
    }

    // getStagesEncadres method fully removed.

    /**
     * Gets all Evaluation records where this Enseignant is the evaluator.
     *
     * @return Evaluation[]
     */
    public function getEvaluationsDonnees(): array
    {
        if (empty($this->id_utilisateur)) { // PK of Enseignant is id_utilisateur
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM evaluation WHERE enseignant_id = :enseignant_id");
        $stmt->bindParam(':enseignant_id', $this->id_utilisateur);
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
     * Gets all ValidationRapport records made by this Enseignant.
     *
     * @return ValidationRapport[]
     */
    public function getValidationsRapport(): array
    {
        if (empty($this->id_utilisateur)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM validation_rapport WHERE utilisateur_id = :utilisateur_id");
        $stmt->bindParam(':utilisateur_id', $this->id_utilisateur);
        $stmt->execute();
        $validationsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $validations = [];
        if ($validationsData) {
            foreach ($validationsData as $data) {
                $validation = new ValidationRapport($this->pdo);
                $validation->utilisateur_id = $data['utilisateur_id'];
                $validation->rapport_etudiant_id = $data['rapport_etudiant_id'];
                $validation->date_validation = $data['date_validation'] ?? null;
                $validation->commentaire = $data['commentaire'] ?? null;
                $validations[] = $validation;
            }
        }
        return $validations;
    }

    /**
     * Gets all AffectationEncadrant records for this Enseignant.
     *
     * @return AffectationEncadrant[]
     */
    public function getAffectationsEncadrant(): array
    {
        if (empty($this->id_utilisateur)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM affectation_encadrant WHERE utilisateur_id = :utilisateur_id");
        $stmt->bindParam(':utilisateur_id', $this->id_utilisateur);
        $stmt->execute();
        $affectationsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $affectations = [];
        if ($affectationsData) {
            foreach ($affectationsData as $data) {
                $affectation = new AffectationEncadrant($this->pdo);
                $affectation->utilisateur_id = $data['utilisateur_id'];
                $affectation->rapport_etudiant_id = $data['rapport_etudiant_id'];
                $affectation->statut_jury_id = $data['statut_jury_id'];
                $affectation->date_affectation = $data['date_affectation'] ?? null;
                $affectations[] = $affectation;
            }
        }
        return $affectations;
    }

    /**
     * Gets all HistoriqueFonction records for this Enseignant.
     *
     * @return HistoriqueFonction[]
     */
    public function getHistoriqueFonctions(): array
    {
        if (empty($this->id_utilisateur)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM historique_fonction WHERE utilisateur_id = :utilisateur_id");
        $stmt->bindParam(':utilisateur_id', $this->id_utilisateur);
        $stmt->execute();
        $historiqueData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $historiques = [];
        if ($historiqueData) {
            foreach ($historiqueData as $data) {
                $historique = new HistoriqueFonction($this->pdo);
                $historique->utilisateur_id = $data['utilisateur_id'];
                $historique->fonction_id = $data['fonction_id'];
                $historique->date_occupation = $data['date_occupation'];
                $historiques[] = $historique;
            }
        }
        return $historiques;
    }

    /**
     * Gets all HistoriqueGrade records for this Enseignant.
     *
     * @return HistoriqueGrade[]
     */
    public function getHistoriqueGrades(): array
    {
        if (empty($this->id_utilisateur)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM historique_grade WHERE utilisateur_id = :utilisateur_id");
        $stmt->bindParam(':utilisateur_id', $this->id_utilisateur);
        $stmt->execute();
        $historiqueData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $historiques = [];
        if ($historiqueData) {
            foreach ($historiqueData as $data) {
                $historique = new HistoriqueGrade($this->pdo);
                $historique->utilisateur_id = $data['utilisateur_id'];
                $historique->grade_id = $data['grade_id'];
                $historique->date_grade = $data['date_grade'];
                $historiques[] = $historique;
            }
        }
        return $historiques;
    }

    /**
     * Gets all RemiseCompteRendu records for this Enseignant.
     *
     * @return RemiseCompteRendu[]
     */
    public function getRemisesCompteRendu(): array
    {
        if (empty($this->id_utilisateur)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM remise_compte_rendu WHERE utilisateur_id = :utilisateur_id");
        $stmt->bindParam(':utilisateur_id', $this->id_utilisateur);
        $stmt->execute();
        $remisesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $remises = [];
        if ($remisesData) {
            foreach ($remisesData as $data) {
                $remise = new RemiseCompteRendu($this->pdo);
                $remise->utilisateur_id = $data['utilisateur_id'];
                $remise->compte_rendu_id = $data['compte_rendu_id'];
                $remise->date_rendu = $data['date_rendu'] ?? null;
                $remises[] = $remise;
            }
        }
        return $remises;
    }

    /**
     * Gets all Messagerie records where this Enseignant is the membre_commission.
     *
     * @return Messagerie[]
     */
    public function getMessagesMessagerieMembre(): array
    {
        if (empty($this->id_utilisateur)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM messagerie WHERE membre_commission_id = :membre_id");
        $stmt->bindParam(':membre_id', $this->id_utilisateur);
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
