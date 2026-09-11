import { Routes, Route, Navigate } from 'react-router-dom'

function PlaceholderPage ({ title }) {
  return (
    <div className='flex h-screen items-center justify-center'>
      <h1 className='text-2xl font-semibold text-brand-dark'>
        {title} — Phase 1-এ real content আসবে
      </h1>
    </div>
  )
}

export default function AppRoutes () {
  return (
    <Routes>
      <Route path='/login' element={<PlaceholderPage title='Admin Login' />} />
      <Route
        path='/dashboard'
        element={<PlaceholderPage title='Dashboard' />}
      />
      <Route path='*' element={<Navigate to='/login' replace />} />
    </Routes>
  )
}
