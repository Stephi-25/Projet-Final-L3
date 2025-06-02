<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Utilisateur; // Added import
use PDO;

/**
 * Class NiveauAccesDonnees
 *
 * Represents the niveau_acces_donnees table.
 *
 * @package App\Models
 */
class NiveauAccesDonnees extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'niveau_acces_donnees';

    /**
     * @var string The ID of the data access level.
     */
    public string $id;

    /**
     * @var string The label of the data access level.
     */
    public string $libelle;

    /**
     * @var string|null A description of the data access level.
     */
    public ?string $description;

    /**
     * NiveauAccesDonnees constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the data access level.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the ID of the data access level.
     *
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the label of the data access level.
     *
     * @return string
     */
    public function getLibelle(): string
    {
        return $this->libelle;
    }

    /**
     * Sets the label of the data access level.
     *
     * @param string $libelle
     */
    public function setLibelle(string $libelle): void
    {
        $this->libelle = $libelle;
    }

    /**
     * Gets the description of the data access level.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Sets the description of the data access level.
     *
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Gets all Utilisateur records related to this NiveauAccesDonnees.
     *
     * @return Utilisateur[]
     */
    public function getUtilisateurs(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE niveau_acces_donnees_id = :niveau_id");
        $stmt->bindParam(':niveau_id', $this->id);
        $stmt->execute();
        $usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = [];
        if ($usersData) {
            foreach ($usersData as $data) {
                $user = new Utilisateur($this->pdo);
                $user->id = $data['id'];
                $user->nom_utilisateur = $data['nom_utilisateur'];
                $user->mot_de_passe = $data['mot_de_passe'];
                $user->email = $data['email'] ?? null;
                $user->date_creation_compte = $data['date_creation_compte'] ?? null;
                $user->date_derniere_connexion = $data['date_derniere_connexion'] ?? null;
                $user->type_utilisateur_id = $data['type_utilisateur_id'] ?? null;
                $user->groupe_utilisateur_id = $data['groupe_utilisateur_id'] ?? null;
                $user->niveau_acces_donnees_id = $data['niveau_acces_donnees_id'] ?? null;
                $users[] = $user;
            }
        }
        return $users;
    }
}
