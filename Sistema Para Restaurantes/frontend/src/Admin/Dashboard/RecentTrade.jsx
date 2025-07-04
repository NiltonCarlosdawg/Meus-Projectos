import axios from "axios"
import { useEffect, useState } from "react"
import { BiEdit, BiTrash } from "react-icons/bi"
import { FiArrowUpRight, FiDollarSign, FiMoreHorizontal } from "react-icons/fi"

const RecentTrade = () => {
  let [dbreserva, setDbReserva] = useState([])

  useEffect(()=>{
    const getReserva=async()=>{
      try {
        const resposta = await axios.get("http://localhost:3000/getreserva")
        setDbReserva(resposta.data.rows)
      } catch (error) {
        console.log(error)
      }
    }
    getReserva()
  },[])
  console.log(dbreserva)
  return (
    <div className="col-span-12 p-4 rounded border border-stone-300">
      <div className="mb-4 flex items-center justify-between">
        <h3 className="flex items-center gap-1.5 font-medium"><FiDollarSign/> Reservas feitas</h3>
        <button className="text-sm text-violet-500 hover:underline">See all</button>
      </div>
      <table className="w-full table-auto">
        <Tablehead/>
        <tbody>
        {dbreserva.map((item, index)=>{
            return(
              <TableRow key={index}
              id_reserva={item.id_reserva}
              cusId={item.nome_reserva}
              email={item.email_reserva}
              phone={item.phone_reserva}
              data={item.data_reserva}
              hora={item.hora_reserva}
              pessoas={item.pessoa_reserva}
              ocasion={item.ocasion_reserva}
              order={index}
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
        <th className="text-start p-1.5">Customer</th>
        <th className="text-start p-1.5">email</th>
        <th className="text-start p-1.5">phone</th>
        <th className="text-start p-1.5">Data</th>
        <th className="text-start p-1.5">Hora</th>
        <th className="text-start p-1.5">Pessoas</th>
        <th className="text-start p-1.5">Ocasião</th>
        <th className="w-8 p-1.5"></th>
      </tr>
    </thead>
    )
}
const TableRow = ({id_reserva ,cusId, email, phone, data, hora, pessoas, ocasion, order})=>{
  let [showbuttons, setShowButtons] = useState(false)
  const deleteReserva=async()=>{
    try {
      const resposta = await axios.delete(`http://localhost:3000/delete/${id_reserva}`)
      console.log(resposta.data)
      location.reload()
    } catch (error) {
      console.log(error)
    }
  }
  return <tr className={order % 2?"bg-stone-200 text-sm":"text-sm"}>
    <td>
    {cusId}
    </td>
    <td className="p-1.5">{email}</td>
    <td className="p-1.5">{phone}</td>
    <td className="p-1.5">{data}</td>
    <td className="p-1.5">{hora}</td>
    <td className="p-1.5">{pessoas}</td>
    <td className="p-1.5">{ocasion}</td>
    <td className="w-8 p-1.5">
      <button className=" transition-colors grid place-content-center rounded text-sm size-8" onClick={()=>setShowButtons(!showbuttons)}>
          <div className="absolute flex right-[3rem] rounded gap-2 p-2 transition-all">
            <button onClick={deleteReserva}><BiTrash color="red" size={20} className="hover:scale-[1.2]"/></button>
            <button><BiEdit color="blue" size={20} className="hover:scale-[1.2]"/></button>
          </div>
   
      </button>
    </td>
  </tr>
}

export default RecentTrade