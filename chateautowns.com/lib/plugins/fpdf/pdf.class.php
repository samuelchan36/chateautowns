<?php

	class pdf extends fpdf{


		function TextWithDirection($x, $y, $txt, $direction='R')
		{
			if ($direction=='R')
				$s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',1,0,0,1,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
			elseif ($direction=='L')
				$s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',-1,0,0,-1,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
			elseif ($direction=='U')
				$s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',0,1,-1,0,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
			elseif ($direction=='D')
				$s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',0,-1,1,0,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
			else
				$s=sprintf('BT %.2F %.2F Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
			if ($this->ColorFlag)
				$s='q '.$this->TextColor.' '.$s.' Q';
			$this->_out($s);
		}

		function TextWithRotation($x, $y, $txt, $txt_angle, $font_angle=0)
		{
			$font_angle+=90+$txt_angle;
			$txt_angle*=M_PI/180;
			$font_angle*=M_PI/180;

			$txt_dx=cos($txt_angle);
			$txt_dy=sin($txt_angle);
			$font_dx=cos($font_angle);
			$font_dy=sin($font_angle);

			$s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',$txt_dx,$txt_dy,$font_dx,$font_dy,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
			if ($this->ColorFlag)
				$s='q '.$this->TextColor.' '.$s.' Q';
			$this->_out($s);
		}

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