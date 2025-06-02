<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Etudiant;
use App\Models\NiveauEtude;
use App\Models\AnneeAcademique;
use PDO;

/**
 * Class InscriptionEtudiant
 *
 * Represents the inscription_etudiant table (student enrollment).
 * This table has a composite primary key (id_etudiant, id_annee_academique, id_niveau_etude).
 * The BaseModel may need adjustments to handle composite keys for find, update, delete.
 *
 * @package App\Models
 */
class InscriptionEtudiant extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'inscription_etudiant';

    /**
     * @var string The ID of the student (part of composite PK).
     */
    public string $id_etudiant;

    /**
     * @var string The ID of the academic year (part of composite PK).
     */
    public string $id_annee_academique;

    /**
     * @var string The ID of the study level (part of composite PK).
     */
    public string $id_niveau_etude;

    /**
     * @var string|null The date of enrollment.
     */
    public ?string $date_inscription; // Assuming DATE SQL type

    /**
     * @var string|null The status of the enrollment.
     */
    public ?string $statut_inscription;


    /**
     * InscriptionEtudiant constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the student.
     * @return string
     */
    public function getIdEtudiant(): string
    {
        return $this->id_etudiant;
    }

    /**
     * Sets the ID of the student.
     * @param string $id_etudiant
     */
    public function setIdEtudiant(string $id_etudiant): void
    {
        $this->id_etudiant = $id_etudiant;
    }

    /**
     * Gets the ID of the academic year.
     * @return string
     */
    public function getIdAnneeAcademique(): string
    {
        return $this->id_annee_academique;
    }

    /**
     * Sets the ID of the academic year.
     * @param string $id_annee_academique
     */
    public function setIdAnneeAcademique(string $id_annee_academique): void
    {
        $this->id_annee_academique = $id_annee_academique;
    }

    /**
     * Gets the ID of the study level.
     * @return string
     */
    public function getIdNiveauEtude(): string
    {
        return $this->id_niveau_etude;
    }

    /**
     * Sets the ID of the study level.
     * @param string $id_niveau_etude
     */
    public function setIdNiveauEtude(string $id_niveau_etude): void
    {
        $this->id_niveau_etude = $id_niveau_etude;
    }

    /**
     * Gets the date of enrollment.
     * @return string|null
     */
    public function getDateInscription(): ?string
    {
        return $this->date_inscription;
    }

    /**
     * Sets the date of enrollment.
     * @param string|null $date_inscription
     */
    public function setDateInscription(?string $date_inscription): void
    {
        $this->date_inscription = $date_inscription;
    }

    /**
     * Gets the status of the enrollment.
     * @return string|null
     */
    public function getStatutInscription(): ?string
    {
        return $this->statut_inscription;
    }

    /**
     * Sets the status of the enrollment.
     * @param string|null $statut_inscription
     */
    public function setStatutInscription(?string $statut_inscription): void
    {
        $this->statut_inscription = $statut_inscription;
    }

    /**
     * Gets the related Etudiant.
     * @return Etudiant|null
     */
    public function getEtudiant(): ?Etudiant
    {
        if (empty($this->id_etudiant)) {
            return null;
        }
        // Note: etudiant table's PK is id_utilisateur
        $stmt = $this->pdo->prepare("SELECT * FROM etudiant WHERE id_utilisateur = :id_etudiant");
        $stmt->bindParam(':id_etudiant', $this->id_etudiant);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $etudiant = new Etudiant($this->pdo);
        $etudiant->id_utilisateur = $data['id_utilisateur'];
        $etudiant->ine = $data['ine'];
        $etudiant->nom = $data['nom'];
        $etudiant->prenom = $data['prenom'];
        $etudiant->date_naissance = $data['date_naissance'] ?? null;
        $etudiant->lieu_naissance = $data['lieu_naissance'] ?? null;
        $etudiant->contact = $data['contact'] ?? null;
        return $etudiant;
    }

    /**
     * Gets the related NiveauEtude.
     * @return NiveauEtude|null
     */
    public function getNiveauEtude(): ?NiveauEtude
    {
        if (empty($this->id_niveau_etude)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM niveau_etude WHERE id = :id");
        $stmt->bindParam(':id', $this->id_niveau_etude);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $niveauEtude = new NiveauEtude($this->pdo);
        $niveauEtude->id = $data['id'];
        $niveauEtude->libelle = $data['libelle'];
        return $niveauEtude;
    }

    /**
     * Gets the related AnneeAcademique.
     * @return AnneeAcademique|null
     */
    public function getAnneeAcademique(): ?AnneeAcademique
    {
        if (empty($this->id_annee_academique)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM annee_academique WHERE id = :id");
        $stmt->bindParam(':id', $this->id_annee_academique);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $anneeAcademique = new AnneeAcademique($this->pdo);
        $anneeAcademique->id = $data['id'];
        $anneeAcademique->libelle = $data['libelle'];
        $anneeAcademique->date_debut = $data['date_debut'];
        $anneeAcademique->date_fin = $data['date_fin'];
        return $anneeAcademique;
    }
}
