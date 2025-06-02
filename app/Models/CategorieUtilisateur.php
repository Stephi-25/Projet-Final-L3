<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\TypeUtilisateur; // Added import
use PDO;

/**
 * Class CategorieUtilisateur
 *
 * Represents the categorie_utilisateur table.
 *
 * @package App\Models
 */
class CategorieUtilisateur extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'categorie_utilisateur';

    /**
     * @var string The ID of the user category.
     */
    public string $id;

    /**
     * @var string The label of the user category.
     */
    public string $libelle;

    /**
     * CategorieUtilisateur constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the user category.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the ID of the user category.
     *
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the label of the user category.
     *
     * @return string
     */
    public function getLibelle(): string
    {
        return $this->libelle;
    }

    /**
     * Sets the label of the user category.
     *
     * @param string $libelle
     */
    public function setLibelle(string $libelle): void
    {
        $this->libelle = $libelle;
    }

    /**
     * Gets all TypeUtilisateur records related to this CategorieUtilisateur.
     *
     * @return TypeUtilisateur[]
     */
    public function getTypeUtilisateurs(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM type_utilisateur WHERE categorie_utilisateur_id = :categorie_id");
        $stmt->bindParam(':categorie_id', $this->id);
        $stmt->execute();
        $typesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $types = [];
        if ($typesData) {
            foreach ($typesData as $data) {
                $type = new TypeUtilisateur($this->pdo);
                $type->id = $data['id'];
                $type->libelle = $data['libelle'];
                $type->categorie_utilisateur_id = $data['categorie_utilisateur_id'];
                // Assuming TypeUtilisateur only has id, libelle, and categorie_utilisateur_id
                $types[] = $type;
            }
        }
        return $types;
    }
}
