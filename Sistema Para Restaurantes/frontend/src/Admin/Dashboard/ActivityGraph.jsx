import axios from "axios"
import { useEffect, useState } from "react"
import { BiEdit, BiTrash, BiUser } from "react-icons/bi"
import { FiArrowUpRight, FiDollarSign, FiMoreHorizontal } from "react-icons/fi"

const ActivityGraph = () => {
  let [dbUser, setDbUser] = useState([])
  useEffect(()=>{
    const getUsers = async()=>{
      try {
        const resposta = await axios.get("http://localhost:3000/getuser")
        setDbUser(resposta.data.rows)
      } catch (error) {
        console.log(error)
      }
    }
    getUsers()
  },[])
  console.log(dbUser)
  return (
    <div className="col-span-8 p-4 rounded border border-stone-300">
      <div className="mb-4 flex items-center justify-between">
        <h3 className="flex items-center gap-1.5 font-medium"><BiUser/> Usuários cadastrados</h3>
        <button className="text-sm text-violet-500 hover:underline">See all</button>
      </div>
      <table className="w-full table-auto">
        <Tablehead/>
        <tbody>
            {dbUser.map((item, index)=>{
              return(
                <TableRow key={index} id_user={item.id_user}
                cusId={item.nome_user}
                sku={item.email_user}
                date="Aug 2nd"
                order={index === 0?1:index===1?4:1}
              />
              )
            }) }
        </tbody>
      </table>
    </div>
  )
}

const Tablehead =()=>{
  return (
  <thead>
      <tr className="text-sm font-normal text-stone-500">
        <th className="text-start p-1.5">Cliente</th>
        <th className="text-start p-1.5">Email</th>
        <th className="text-start p-1.5">Time in</th>
        <th className="w-8"></th>
      </tr>
    </thead>
    )
}
const TableRow = ({id_user,cusId, sku, date, order})=>{
  let [showbuttons, setShowButtons] = useState(false)
  const deleteReserva=async()=>{
    try {
      const resposta = await axios.delete(`http://localhost:3000/deleteuser/${id_user}`)
      console.log(resposta.data)
      location.reload()
    } catch (error) {
      console.log(error)
    }
  }
  return <tr className={order % 2?"bg-stone-200 text-sm":"text-sm"}>
    <td>
      <a href="#" className="text-violet-600 underline flex items-center gap-1">
        {cusId}<FiArrowUpRight/>
      </a>
    </td>
    <td className="p-1.5">{sku}</td>
    <td className="p-1.5">{date}</td>
    <td className="w-12">
    <button className=" transition-colors grid place-content-center rounded text-sm size-8" onClick={()=>setShowButtons(!showbuttons)}>
          <div className="flex right-[3rem] rounded gap-2 p-2 transition-all">
            <button onClick={deleteReserva}><BiTrash color="red" size={20} className="hover:scale-[1.2]"/></button>
            <button><BiEdit color="blue" size={20} className="hover:scale-[1.2]"/></button>
          </div>
   
      </button>
    </td>
  </tr>
}


export default ActivityGraph