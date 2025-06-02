<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Utilisateur;
use PDO;

/**
 * Class Notification
 *
 * Represents the notification table.
 * Composite PK: (emetteur_id, recepteur_id, date_notification).
 *
 * @package App\Models
 */
class Notification extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'notification';

    /**
     * @var string The ID of the sender (FK to utilisateur.id, part of CPK).
     */
    public string $emetteur_id;

    /**
     * @var string The ID of the receiver (FK to utilisateur.id, part of CPK).
     */
    public string $recepteur_id;

    /**
     * @var string|null The message content.
     */
    public ?string $message; // DDL TEXT

    /**
     * @var string The date of the notification (part of CPK).
     */
    public string $date_notification; // DDL DATETIME

    /**
     * Notification constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    // Getters and Setters

    public function getEmetteurId(): string
    {
        return $this->emetteur_id;
    }

    public function setEmetteurId(string $emetteur_id): void
    {
        $this->emetteur_id = $emetteur_id;
    }

    public function getRecepteurId(): string
    {
        return $this->recepteur_id;
    }

    public function setRecepteurId(string $recepteur_id): void
    {
        $this->recepteur_id = $recepteur_id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getDateNotification(): string
    {
        return $this->date_notification;
    }

    public function setDateNotification(string $date_notification): void
    {
        $this->date_notification = $date_notification;
    }

    // Relationship methods

    /**
     * Gets the sender (Emetteur) of the notification.
     * @return Utilisateur|null
     */
    public function getEmetteur(): ?Utilisateur
    {
        if (empty($this->emetteur_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id = :id");
        $stmt->bindParam(':id', $this->emetteur_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $utilisateur = new Utilisateur($this->pdo);
        $utilisateur->id = $data['id'];
        // Populate other Utilisateur properties as needed (it's lean now)
        return $utilisateur;
    }

    /**
     * Gets the receiver (Recepteur) of the notification.
     * @return Utilisateur|null
     */
    public function getRecepteur(): ?Utilisateur
    {
        if (empty($this->recepteur_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id = :id");
        $stmt->bindParam(':id', $this->recepteur_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $utilisateur = new Utilisateur($this->pdo);
        $utilisateur->id = $data['id'];
        // Populate other Utilisateur properties as needed (it's lean now)
        return $utilisateur;
    }
}
