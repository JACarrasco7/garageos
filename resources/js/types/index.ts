export interface Vehicle {
  id: number
  plate: string
  brand: string
  model: string
  year: number
  fuel_type: 'gasolina' | 'diesel' | 'hibrido' | 'electrico' | 'glp'
  color?: string
  current_km: number
  is_active: boolean
  specs?: VehicleSpec
  documents?: Document[]
}

export interface VehicleSpec {
  engine_cc?: number
  power_hp?: number
  torque_nm?: number
  transmission?: 'manual' | 'automatico' | 'cvt'
  drive?: 'fwd' | 'rwd' | '4wd' | 'awd'
  doors?: number
  seats?: number
}

export interface Document {
  id: number
  type: 'factura' | 'itv' | 'seguro' | 'impuesto' | 'otro'
  title: string
  file_path: string
  expiry_date?: string
  amount?: number
}

export interface AlertRule {
  id: number
  type: 'itv' | 'seguro' | 'aceite' | 'neumaticos' | 'revision' | 'impuesto' | 'bateria' | 'custom'
  trigger_km?: number
  trigger_date?: string
  advance_days: number
  advance_km: number
  is_active: boolean
}