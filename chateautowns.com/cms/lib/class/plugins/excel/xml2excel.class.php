<?php 
	class CXML2Excel {

		var $mName = "";

		var $mAuthor = "Lucian Grecu";
		var $mCompany = "The Brand Factory";
		var $mTimeStamp = 0;

		var $mStyles = array();
		var $mDefaultStyle = array();
		var $mWorksheets = array();
		var $mRows = array();
		var $mColumns = array();

		function CXML2Excel($name) {
			$this->mName = $name;
		}

		/** comment here */
		function setDefaultStyle($style) {
			$this->mDefaultStyle = $style;
		}

		/** comment here */
		function addStyle($styles, $id) {
			$this->mStyles[$id] = array("s".$id, explode(";", $styles));
		}

		/** comment here */
		function addRow($row, $styleid, $ws=1) {
			$this->mRows[$ws][] =array($row, $styleid);
		}

		/** comment here */
		function display() {

			$txt = '<?xml version="1.0"?>
			<?mso-application progid="Excel.Sheet"?>
			<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"  xmlns:o="urn:schemas-microsoft-com:office:office"  xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"  xmlns:html="http://www.w3.org/TR/REC-html40">';
			$txt .= $this->buildDocumentProperties();
			$txt .= '<ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
							  <WindowHeight>8535</WindowHeight>
							  <WindowWidth>12345</WindowWidth>
							  <WindowTopX>480</WindowTopX>
							  <WindowTopY>90</WindowTopY>
							  <ProtectStructure>True</ProtectStructure>
							  <ProtectWindows>False</ProtectWindows>
						  </ExcelWorkbook>
';
			$txt .= $this->buildStyles();
			$txt .= $this->buildWorksheet();
			$txt .= '</Workbook>';
			Return $txt;
		}

		/** comment here */
		function buildStyles() {
			$txt = '<Styles>';
			$txt .= '<Style ss:ID="Default" ss:Name="Normal">'.$this->mDefaultStyle.'</Style>';
			foreach ($this->mStyles as $key=>$val) {
				$txt .= '<Style ss:ID="'.$val[0].'" >';
				$txt .= implode("", $val[1]);
				$txt .= '</Style>';
			}
			$txt .= '</Styles>';
			Return $txt;
			die($txt);
		}


		/** comment here */
		function buildDocumentProperties() {
			$txt = '	  <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
							 <Author>'.$this->mAuthor.'</Author>
							  <Created>2007-01-01T01:01:01Z</Created>
							  <Company>'.$this->mCompany.'</Company>
							  <Version>1</Version>
						  </DocumentProperties>
					';
		}

		/** comment here */
		function addWorksheet($name, $styleID) {
			$this->mWorksheets[] = array($name, $styleID);
		}

		/** comment here */
		function addColumn($idx, $props) {
			$txt = '<Column ss:Index="'.$idx.'"';
			foreach ($props as $key=>$val) {
				$txt.= ' ss:'.$key.'="'.$val.'"';
			}
			$txt .= '/>';
			$this->mColumns[$idx] = $txt;
		}

		/** comment here */
		function buildWorksheet() {
			$txt = '<Worksheet ss:Name="'.$this->mWorksheets[0][0].'">';
			$txt .= '<Table ss:ExpandedColumnCount="'.count($this->mRows[1][0][0]).'"  ss:ExpandedRowCount="'.count($this->mRows[1]).'"  x:FullColumns="1" x:FullRows="1" ssStyleID="'.$this->mWorksheets[0][1].'">';

			$txt .= implode("", $this->mColumns);//die($txt);

			foreach ($this->mRows[1] as $key=>$val) {
				$style = "";
				if ($val[1]) $style = 'ss:StyleID="s'.$val[1].'"';
				$txt .= '<Row '. $style.'>';
				foreach ($val[0] as $key2=>$val2) {
					$type = "String";
					if (is_numeric($val2)) $type="Number";
					$txt .= '<Cell><Data ss:Type="'.$type.'">'.xmlentities($val2).'</Data></Cell>';
				}
				$txt .= '</Row>';
			}
			$txt .= ' </Table>
								  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
									  <Print>
										  <ValidPrinterInfo/>
										  <HorizontalResolution>300</HorizontalResolution>
										  <VerticalResolution>300</VerticalResolution>
									  </Print>
									  <Selected/>
									  <Panes>
										  <Pane>
											  <Number>3</Number>
											  <ActiveRow>1</ActiveRow>
										  </Pane>
									  </Panes>
									  <ProtectObjects>False</ProtectObjects>
									  <ProtectScenarios>False</ProtectScenarios>
								  </WorksheetOptions>
								  </Worksheet>
							';
			Return $txt;
		}

	}
?>