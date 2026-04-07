<?php

class Patient {
        private $MaSV;
        private $TenSV;
        private $Lop;

        private $Khoa;

        public function __construct($MaSV, $TenSV, $Lop, $Khoa) {
        $this->MaSV = $MaSV;
        $this->TenSV = $TenSV;
        $this->Lop = $Lop;
        $this->Khoa = $Khoa;

    }

        public function getMaSV() {
        return $this->MaSV;
    }

        public function getTenSV() {
        return $this->TenSV;
    }

        public function getLop() {
            return $this->Lop;
        }

        public function getKhoa() {
            return $this->Khoa;
        }

        public function setTenSV($TenSV) {
             $this->TenSV = $TenSV;
        }

  
    }

?>