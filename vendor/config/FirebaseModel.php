<?php
namespace AnimalShelter\Models;

use Kreait\Firebase\Factory;

class FirebaseModel {
    private $firestore;

    public function __construct() {
        $factory = (new Factory)->withServiceAccount(__DIR__ . 'animalshelterapp-fba77-firebase-adminsdk-fbsvc-8aa1dee65b.json');
        $this->firestore = $factory->createFirestore();
    }

    public function getFirestore() {
        return $this->firestore;
    }
}
