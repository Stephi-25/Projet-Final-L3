<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\InscriptionEtudiant; // Added import
use PDO;

/**
 * Class NiveauEtude
 *
 * Represents the niveau_etude table.
 *
 * @package App\Models
 */
class NiveauEtude extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'niveau_etude';

    /**
     * @var string The ID of the study level.
     */
    public string $id;

    /**
     * @var string The label of the study level.
     */
    public string $libelle;

    /**
     * NiveauEtude constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the study level.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the ID of the study level.
     *
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the label of the study level.
     *
     * @return string
     */
    public function getLibelle(): string
    {
        return $this->libelle;
    }

    /**
     * Sets the label of the study level.
     *
     * @param string $libelle
     */
    public function setLibelle(string $libelle): void
    {
        $this->libelle = $libelle;
    }

    /**
     * Gets all InscriptionEtudiant records for this NiveauEtude.
     *
     * @return InscriptionEtudiant[]
     */
    public function getInscriptionsEtudiant(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM inscription_etudiant WHERE id_niveau_etude = :niveau_id");
        $stmt->bindParam(':niveau_id', $this->id);
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
}
