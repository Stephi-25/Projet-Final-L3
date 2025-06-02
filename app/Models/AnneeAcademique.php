<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\InscriptionEtudiant;
// Removed import for StageEffectue
use PDO;

/**
 * Class AnneeAcademique
 *
 * Represents the annee_academique table.
 *
 * @package App\Models
 */
class AnneeAcademique extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'annee_academique';

    /**
     * @var string The ID of the academic year.
     */
    public string $id;

    /**
     * @var string The label of the academic year (e.g., "2023-2024").
     */
    public string $libelle;

    /**
     * @var string The start date of the academic year.
     */
    public string $date_debut;

    /**
     * @var string The end date of the academic year.
     */
    public string $date_fin;

    /**
     * AnneeAcademique constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the academic year.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the ID of the academic year.
     *
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the label of the academic year.
     *
     * @return string
     */
    public function getLibelle(): string
    {
        return $this->libelle;
    }

    /**
     * Sets the label of the academic year.
     *
     * @param string $libelle
     */
    public function setLibelle(string $libelle): void
    {
        $this->libelle = $libelle;
    }

    /**
     * Gets the start date of the academic year.
     *
     * @return string
     */
    public function getDateDebut(): string
    {
        return $this->date_debut;
    }

    /**
     * Sets the start date of the academic year.
     *
     * @param string $date_debut
     */
    public function setDateDebut(string $date_debut): void
    {
        $this->date_debut = $date_debut;
    }

    /**
     * Gets the end date of the academic year.
     *
     * @return string
     */
    public function getDateFin(): string
    {
        return $this->date_fin;
    }

    /**
     * Sets the end date of the academic year.
     *
     * @param string $date_fin
     */
    public function setDateFin(string $date_fin): void
    {
        $this->date_fin = $date_fin;
    }

    /**
     * Gets all InscriptionEtudiant records for this AnneeAcademique.
     *
     * @return InscriptionEtudiant[]
     */
    public function getInscriptionsEtudiant(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM inscription_etudiant WHERE id_annee_academique = :annee_id");
        $stmt->bindParam(':annee_id', $this->id);
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
