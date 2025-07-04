import { useState } from "react"
import Dashboard from "./Dashboard/Dashboard"
import Sidebar from "./Sidebar/Sidebar"
import Modal from "./Sidebar/Modal"

const Admin = () => {
  const [openCommand, setOpenCommand] = useState(false)
  const commandOn = ()=>{
    setOpenCommand(!openCommand)
  }
  return (
    <div className="grid gap-4 p-4 grid-cols-[220px,_1fr] bg-stone-100 text-stone-950">
      <Sidebar commandOn={commandOn}/>
      <Dashboard/>
      {openCommand&&<Modal setOpenCommand={setOpenCommand}/>}
    </div>
  )
}

export default Admin