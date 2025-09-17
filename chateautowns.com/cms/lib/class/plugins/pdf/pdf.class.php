<?php

	class pdf extends fpdf{

		/** comment here */
		function keyval($label, $value, $multicell = false) {
			$this->SetFont('Arial','B',10);
			$this->SetFillColor(239);
			$this->SetTextColor(153);
			$this->Cell(3*55,3*7,$label. ":","TBLR",0,"R", 1);
			$this->SetFont('Arial','B',10);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell(9,3*7,"","LTB",0,"L", 1);
			if (!$value) $value = " ";
			if ($multicell) $this->MultiCell(3*112,3*7,$value,"RTB","L", 1); else $this->Cell(3*112,3*7,$value,"RTB",0,"L", 1);
			$this->Ln();
		}

		/** comment here */
		function head($label) {
			$this->SetFont('Arial','B',11);
			$this->SetFillColor(204, 204, 204);
			$this->SetDrawColor(204, 204, 204);
			$this->Cell(3*170,3*7,$label,1,0, "L",1);
			$this->Ln();
//			$this->Line($this->getX(),$this->getY(),$this->getX() + 3*170, $this->getY() + 1);
//			$this->Ln();
		}

		/** comment here */
		function table($headers, $data, $widths) {
			if (!empty($headers)) {
				$this->SetFont('Arial','B',10);
				$this->SetFillColor(239);
				$this->SetTextColor(153);
				foreach ($headers as $key=>$val) {
					$this->Cell($widths[$key],3*7,$val, 1, 0, "C", 1);
				}
				$this->Ln();
			}
			$this->SetFont('Arial','B',10);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			foreach ($data as $key=>$val) {
				foreach ($val as $key2=>$val2) {
					if (!$val2) $val2 = " ";
					$this->Cell($widths[$key2],3*7,$val2, 1, 0, "C", 1);
				}
				for($i=count($val); $i<3; $i++) {
					$this->Cell($widths[$i],3*7," ", 1, 0, "C", 1);
				}
				$this->Ln();
			}
		}

		/** comment here */
		function keyval2($label, $value, $multicell = false) {
			if (!$value) $value = " ";

			$this->SetFont('Arial','B',11);
			$this->SetFillColor(255, 255, 255);
			$this->SetDrawColor(204, 204, 204);
			$this->Cell(3*170,3*7,$label,"B",0, "L",1);
			$this->Ln();

			$this->SetFont('Arial','',10);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Ln();
			$this->MultiCell(3*170,3*7,$value,"0","J", 1);
			$this->Ln();
			$this->Ln();
		}

	}

?>