<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\StageEffectue; // Added import
use PDO;

/**
 * Class Entreprise
 *
 * Represents the entreprise table.
 *
 * @package App\Models
 */
class Entreprise extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'entreprise';

    /**
     * @var string The ID of the company.
     */
    public string $id;

    /**
     * @var string The name of the company.
     */
    public string $nom_entreprise;

    /**
     * @var string|null The address of the company.
     */
    public ?string $adresse_entreprise;

    /**
     * @var string|null The phone number of the company.
     */
    public ?string $telephone_entreprise;

    /**
     * @var string|null The email of the company.
     */
    public ?string $email_entreprise;

    /**
     * @var string|null The sector of activity of the company.
     */
    public ?string $secteur_activite;

    /**
     * Entreprise constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the company.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the ID of the company.
     *
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the name of the company.
     *
     * @return string
     */
    public function getNomEntreprise(): string
    {
        return $this->nom_entreprise;
    }

    /**
     * Sets the name of the company.
     *
     * @param string $nom_entreprise
     */
    public function setNomEntreprise(string $nom_entreprise): void
    {
        $this->nom_entreprise = $nom_entreprise;
    }

    /**
     * Gets the address of the company.
     *
     * @return string|null
     */
    public function getAdresseEntreprise(): ?string
    {
        return $this->adresse_entreprise;
    }

    /**
     * Sets the address of the company.
     *
     * @param string|null $adresse_entreprise
     */
    public function setAdresseEntreprise(?string $adresse_entreprise): void
    {
        $this->adresse_entreprise = $adresse_entreprise;
    }

    /**
     * Gets the phone number of the company.
     *
     * @return string|null
     */
    public function getTelephoneEntreprise(): ?string
    {
        return $this->telephone_entreprise;
    }

    /**
     * Sets the phone number of the company.
     *
     * @param string|null $telephone_entreprise
     */
    public function setTelephoneEntreprise(?string $telephone_entreprise): void
    {
        $this->telephone_entreprise = $telephone_entreprise;
    }

    /**
     * Gets the email of the company.
     *
     * @return string|null
     */
    public function getEmailEntreprise(): ?string
    {
        return $this->email_entreprise;
    }

    /**
     * Sets the email of the company.
     *
     * @param string|null $email_entreprise
     */
    public function setEmailEntreprise(?string $email_entreprise): void
    {
        $this->email_entreprise = $email_entreprise;
    }

    /**
     * Gets the sector of activity of the company.
     *
     * @return string|null
     */
    public function getSecteurActivite(): ?string
    {
        return $this->secteur_activite;
    }

    /**
     * Sets the sector of activity of the company.
     *
     * @param string|null $secteur_activite
     */
    public function setSecteurActivite(?string $secteur_activite): void
    {
        $this->secteur_activite = $secteur_activite;
    }

    /**
     * Gets all StageEffectue records for this Entreprise.
     *
     * @return StageEffectue[]
     */
    public function getStagesEffectues(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM stage_effectue WHERE id_entreprise = :entreprise_id");
        $stmt->bindParam(':entreprise_id', $this->id);
        $stmt->execute();
        $stagesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stages = [];
        if ($stagesData) {
            foreach ($stagesData as $data) {
                $stage = new StageEffectue($this->pdo);
                // Populate StageEffectue based on its corrected properties
                $stage->utilisateur_id = $data['utilisateur_id'] ?? null; // Corrected FK name
                $stage->entreprise_id = $data['entreprise_id'];    // Corrected FK name
                $stage->date_debut = $data['date_debut'] ?? null;    // Corrected prop name
                $stage->date_fin = $data['date_fin'] ?? null;      // Corrected prop name
                $stages[] = $stage;
            }
        }
        return $stages;
    }
}
