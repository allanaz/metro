
// Train interfaces
export interface Train {
    length: string;
    destination: string;
    line: string | null;
    arrival: string;
  }
  
export interface TrainResponse {
    status: 'success' | 'error';
    data?: Train[];
    message?: string;
  }
  
// Station interfaces
export interface Station {
    code: string;
    name: string;
    stationTogether1: string | null;
    lineCode1: string;
    lineCode2: string | null;
    lineCode3: string | null;
  }
  
export interface StationResponse {
    status: 'success' | 'error';
    data?: Station[];
    message?: string;
  }

  
export interface StationItem {
    title: string;
    value: Station;
    raw: Station;
  }
  

export interface TrainHeader {
    title: string;
    key: string;
    width?: string;
  }
  
export const LINE_COLORS: Record<string, string> = {
    'RD': 'red',
    'BL': 'blue',
    'OR': 'orange',
    'SV': 'grey',
    'GR': 'green',
    'YL': 'yellow'
  };
  
export function getStationLines(station: Station | null): string[] {
    if (!station) return [];
    
    const lines: string[] = [];
    if (station.lineCode1) lines.push(station.lineCode1);
    if (station.lineCode2) lines.push(station.lineCode2);
    if (station.lineCode3) lines.push(station.lineCode3);
    
    return lines;
  }
  
export function getLineColor(lineCode: string | null): string {
    if (!lineCode) return 'grey';
    return LINE_COLORS[lineCode] || 'grey';
  }
