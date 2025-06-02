<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Utilisateur;
use App\Models\ApprobationRapport; // Added import
use PDO;

/**
 * Class PersonnelAdministratif
 *
 * Represents the personnel_administratif table.
 *
 * @package App\Models
 */
class PersonnelAdministratif extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'personnel_administratif';

    /**
     * @var string The ID of the administrative staff. This is also a foreign key to utilisateur.id.
     */
    public string $id_utilisateur;

    // Removed nom, prenom, contact, id_fonction properties

    /**
     * PersonnelAdministratif constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the administrative staff (references utilisateur.id).
     * @return string
     */
    public function getIdUtilisateur(): string
    {
        return $this->id_utilisateur;
    }

    /**
     * Sets the ID of the administrative staff.
     * @param string $id_utilisateur
     */
    public function setIdUtilisateur(string $id_utilisateur): void
    {
        $this->id_utilisateur = $id_utilisateur;
        // Since this is the PK for this table in the SQL, 
        // and BaseModel uses 'id' by convention for find/update/delete,
        // we might need to alias this or override BaseModel methods if 'id' is expected.
        // For now, we assume 'id_utilisateur' is the effective PK here.
    }

    // Removed getters and setters for nom, prenom, contact, id_fonction

    /**
     * Gets the related Utilisateur for this PersonnelAdministratif.
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
        $utilisateur->type_utilisateur_id = $data['type_utilisateur_id'] ?? null; // Corrected based on Utilisateur change
        $utilisateur->groupe_utilisateur_id = $data['groupe_utilisateur_id'] ?? null;
        $utilisateur->niveau_acces_donnees_id = $data['niveau_acces_donnees_id'] ?? null;
        return $utilisateur;
    }

    /**
     * Gets all ApprobationRapport records made by this PersonnelAdministratif.
     *
     * @return ApprobationRapport[]
     */
    public function getApprobationsRapport(): array
    {
        if (empty($this->id_utilisateur)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM approbation_rapport WHERE utilisateur_id = :utilisateur_id");
        $stmt->bindParam(':utilisateur_id', $this->id_utilisateur);
        $stmt->execute();
        $approbationsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $approbations = [];
        if ($approbationsData) {
            foreach ($approbationsData as $data) {
                $approbation = new ApprobationRapport($this->pdo);
                $approbation->utilisateur_id = $data['utilisateur_id'];
                $approbation->rapport_etudiant_id = $data['rapport_etudiant_id'];
                $approbation->date_approbation = $data['date_approbation'] ?? null;
                $approbations[] = $approbation;
            }
        }
        return $approbations;
    }
}
