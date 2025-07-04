import axios from "axios"
import { useEffect, useState } from "react"
import { FiTrendingDown, FiTrendingUp } from "react-icons/fi"

const StateCards = () => {
  let [tableuser, setTableUser] = useState([])
  let [tablereserva, setTableReserva] = useState([])
  useEffect(()=>{
    const getDB =async()=>{
      try {
        let resposta = await axios.get("http://localhost:3000/getuser")
        let reserve = await axios.get("http://localhost:3000/getreserva")
        setTableReserva(reserve.data.rows)
        setTableUser(resposta.data.rows)
        console.log(resposta.data, reserve.data)
      } catch (error) {
        console.log(error)
      }


    }
    getDB()
  },[])
  return (
    <>
    <Card 
      title="Clientes Cadastrados"
      value={tableuser.length}
      pillText={`${tableuser.length / 100}%`}
      trend="up"
      period="From Jan 1st - jul 31st"
    />
    <Card 
      title="Reservas feitas"
      value={tablereserva.length}
      pillText={`${tablereserva.length / 100}%`}
      trend="up"
      period="From Jan 1st - jul 31st"
    />
    <Card 
      title="Visitantes no site"
      value="1"
      pillText="0.75%"
      trend="up"
      period="Previous 365 days"
    />
    </>
  )
}

const Card = ({title, value, pillText, trend, period})=>{
  return <div className="col-span-4 p-4 rounded border border-stone-300">
    <div className="flex mb-8 items-start justify-between">
      <div>
        <h3 className="text-stone-500 mb-2 text-sm">
          {title}
        </h3>
        <p className="text-3xl font-semibold">{value}</p>
      </div>
      <span className={`text-xs flex items-center gap-1 font-medium px-2 py-1 rounded ${
      trend === "up"?"bg-green-100 text-green-700":"bg-red-100 text-red-700"}`
      }>
        {trend === "up"?<FiTrendingUp/>:<FiTrendingDown/>}
        {pillText}
      </span>
    </div>
    <p className="text-xs text-stone-500">
      {period}
    </p>
  </div>
}

export default StateCards