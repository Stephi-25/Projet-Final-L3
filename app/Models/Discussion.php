<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Messagerie; // Added import
use PDO;

/**
 * Class Discussion
 *
 * Represents the discussion table.
 *
 * @package App\Models
 */
class Discussion extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'discussion';

    /**
     * @var string The ID of the discussion.
     */
    public string $id;

    /**
     * @var string|null The content of the message.
     */
    public ?string $message;

    /**
     * @var string|null The timestamp of the message.
     */
    public ?string $timestamp_message; // Assuming DATETIME or TIMESTAMP SQL type maps to string

    /**
     * @var string|null The ID of the sender (utilisateur).
     */
    public ?string $id_utilisateur_expediteur;

    /**
     * @var string|null The ID of the recipient (utilisateur).
     */
    public ?string $id_utilisateur_destinataire;

    /**
     * @var string|null The ID of the related report (rapport_etudiant).
     */
    public ?string $id_rapport_etudiant;


    /**
     * Discussion constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the discussion.
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the ID of the discussion.
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the message content.
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Sets the message content.
     * @param string|null $message
     */
    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    /**
     * Gets the timestamp of the message.
     * @return string|null
     */
    public function getTimestampMessage(): ?string
    {
        return $this->timestamp_message;
    }

    /**
     * Sets the timestamp of the message.
     * @param string|null $timestamp_message
     */
    public function setTimestampMessage(?string $timestamp_message): void
    {
        $this->timestamp_message = $timestamp_message;
    }

    /**
     * Gets the ID of the sender.
     * @return string|null
     */
    public function getIdUtilisateurExpediteur(): ?string
    {
        return $this->id_utilisateur_expediteur;
    }

    /**
     * Sets the ID of the sender.
     * @param string|null $id_utilisateur_expediteur
     */
    public function setIdUtilisateurExpediteur(?string $id_utilisateur_expediteur): void
    {
        $this->id_utilisateur_expediteur = $id_utilisateur_expediteur;
    }

    /**
     * Gets the ID of the recipient.
     * @return string|null
     */
    public function getIdUtilisateurDestinataire(): ?string
    {
        return $this->id_utilisateur_destinataire;
    }

    /**
     * Sets the ID of the recipient.
     * @param string|null $id_utilisateur_destinataire
     */
    public function setIdUtilisateurDestinataire(?string $id_utilisateur_destinataire): void
    {
        $this->id_utilisateur_destinataire = $id_utilisateur_destinataire;
    }

    /**
     * Gets the ID of the related report.
     * @return string|null
     */
    public function getIdRapportEtudiant(): ?string
    {
        return $this->id_rapport_etudiant;
    }

    /**
     * Sets the ID of the related report.
     * @param string|null $id_rapport_etudiant
     */
    public function setIdRapportEtudiant(?string $id_rapport_etudiant): void
    {
        $this->id_rapport_etudiant = $id_rapport_etudiant;
    }

    /**
     * Gets all Messagerie records related to this Discussion.
     *
     * @return Messagerie[]
     */
    public function getMessages(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM messagerie WHERE discussion_id = :discussion_id");
        $stmt->bindParam(':discussion_id', $this->id);
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
