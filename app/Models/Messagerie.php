<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Enseignant;
use App\Models\Etudiant;
use App\Models\Discussion;
use PDO;

/**
 * Class Messagerie
 *
 * Represents the messagerie table.
 * Composite PK: (membre_commission_id, etudiant_concerne_id, discussion_id, date_message).
 *
 * @package App\Models
 */
class Messagerie extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'messagerie';

    /**
     * @var string FK to enseignant.utilisateur_id, part of CPK.
     */
    public string $membre_commission_id;

    /**
     * @var string FK to etudiant.utilisateur_id, part of CPK.
     */
    public string $etudiant_concerne_id;

    /**
     * @var string FK to discussion.id, part of CPK.
     */
    public string $discussion_id;

    /**
     * @var string|null The message content.
     */
    public ?string $message; // DDL TEXT

    /**
     * @var string The date of the message (part of CPK).
     */
    public string $date_message; // DDL DATETIME

    /**
     * Messagerie constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    // Getters and Setters

    public function getMembreCommissionId(): string
    {
        return $this->membre_commission_id;
    }

    public function setMembreCommissionId(string $membre_commission_id): void
    {
        $this->membre_commission_id = $membre_commission_id;
    }

    public function getEtudiantConcerneId(): string
    {
        return $this->etudiant_concerne_id;
    }

    public function setEtudiantConcerneId(string $etudiant_concerne_id): void
    {
        $this->etudiant_concerne_id = $etudiant_concerne_id;
    }

    public function getDiscussionId(): string
    {
        return $this->discussion_id;
    }

    public function setDiscussionId(string $discussion_id): void
    {
        $this->discussion_id = $discussion_id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getDateMessage(): string
    {
        return $this->date_message;
    }

    public function setDateMessage(string $date_message): void
    {
        $this->date_message = $date_message;
    }

    // Relationship methods

    /**
     * Gets the related Enseignant (MembreCommission).
     * @return Enseignant|null
     */
    public function getMembreCommission(): ?Enseignant
    {
        if (empty($this->membre_commission_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM enseignant WHERE id_utilisateur = :id");
        $stmt->bindParam(':id', $this->membre_commission_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $enseignant = new Enseignant($this->pdo);
        $enseignant->id_utilisateur = $data['id_utilisateur']; // Enseignant is lean
        return $enseignant;
    }

    /**
     * Gets the related Etudiant (EtudiantConcerne).
     * @return Etudiant|null
     */
    public function getEtudiantConcerne(): ?Etudiant
    {
        if (empty($this->etudiant_concerne_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM etudiant WHERE id_utilisateur = :id");
        $stmt->bindParam(':id', $this->etudiant_concerne_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $etudiant = new Etudiant($this->pdo);
        $etudiant->id_utilisateur = $data['id_utilisateur']; // Etudiant is lean
        return $etudiant;
    }

    /**
     * Gets the related Discussion.
     * @return Discussion|null
     */
    public function getDiscussion(): ?Discussion
    {
        if (empty($this->discussion_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM discussion WHERE id = :id");
        $stmt->bindParam(':id', $this->discussion_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $discussion = new Discussion($this->pdo);
        $discussion->id = $data['id'];
        // Assuming Discussion model has at least 'id'. Populate other fields if known from its DDL.
        // For now, based on current Discussion.php:
        $discussion->message = $data['message'] ?? null;
        $discussion->timestamp_message = $data['timestamp_message'] ?? null;
        $discussion->id_utilisateur_expediteur = $data['id_utilisateur_expediteur'] ?? null;
        $discussion->id_utilisateur_destinataire = $data['id_utilisateur_destinataire'] ?? null;
        $discussion->id_rapport_etudiant = $data['id_rapport_etudiant'] ?? null;
        return $discussion;
    }
}
